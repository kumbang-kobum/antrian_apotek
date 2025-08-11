<?php
/**
 * panggil/send_wa_pasien.php
 * Kirim WhatsApp ke pasien: "obat siap diambil"
 * - Input POST: jenis ("Non Racik" | "Racik"), no_antrian (string/nomor)
 * - Output: JSON { ok: bool, ... }
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// ===== Helper keluaran JSON rapi =====
function out(array $arr, int $code = 200): void {
  http_response_code($code);
  if (function_exists('ob_get_length') && ob_get_length()) { ob_clean(); }
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

// ===== Konfigurasi WAHA (bisa diset ENV, ada default) =====
$WAHA_URL = rtrim(getenv('WAHA_URL') ?: 'https://xxxx.xxxxxx', '/');
$WAHA_SESSION = getenv('WAHA_SESSION') ?: 'default'; // samakan dengan yang aktif di WAHA

// ===== Validasi input =====
$jenis = $_POST['jenis']      ?? '';
$no    = $_POST['no_antrian'] ?? '';

$jenis = trim((string)$jenis);
$no    = trim((string)$no);

if ($jenis === '' || $no === '') {
  out(['ok'=>false, 'error'=>'Parameter kurang (jenis / no_antrian)'], 400);
}
// Normalisasi label resep di DB (pastikan cocok dengan isi kolom antrian)
if (!in_array($jenis, ['Non Racik','Racik'], true)) {
  out(['ok'=>false, 'error'=>'Jenis tidak valid. Gunakan "Non Racik" atau "Racik"'], 422);
}

// ===== Koneksi DB =====
$cfg = __DIR__ . '/../config/database.php';
if (!file_exists($cfg)) out(['ok'=>false,'error'=>'config/database.php tidak ditemukan'], 500);
require_once $cfg; // harus mendefinisikan $pdo (PDO)

try {
  // safety: set error mode
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  out(['ok'=>false,'error'=>'PDO belum siap: '.$e->getMessage()], 500);
}

// ===== Ambil data pasien berdasarkan antrian hari ini =====
// Tabel asumsi: antrian_farmasi_rajal(no_antrian, resep, tgl_antri, no_rawat)
// Join ke reg_periksa(no_rawat, no_rkm_medis), pasien(no_rkm_medis, nm_pasien, no_tlp)
try {
  $sql = "
    SELECT p.nm_pasien,
           IFNULL(p.no_tlp,'') AS no_tlp,
           r.no_rawat
    FROM antrian_farmasi_rajal a
    JOIN reg_periksa r ON r.no_rawat = a.no_rawat
    JOIN pasien p      ON p.no_rkm_medis = r.no_rkm_medis
    WHERE a.no_antrian = :no
      AND a.resep      = :jenis
      AND a.tgl_antri  = CURDATE()
    LIMIT 1
  ";
  $st = $pdo->prepare($sql);
  $st->execute([':no'=>$no, ':jenis'=>$jenis]);
  $row = $st->fetch();
  if (!$row) {
    out(['ok'=>false,'error'=>'Data antrian tidak ditemukan untuk hari ini'], 404);
  }

  $nama = (string)($row['nm_pasien'] ?? '');
  $telp = (string)($row['no_tlp'] ?? '');
  $no_rawat = (string)($row['no_rawat'] ?? '');
  // Bersihkan nomor -> hanya digit
  $telp = preg_replace('/\D+/', '', $telp ?? '');
  if ($telp === '') {
    out(['ok'=>false,'error'=>'Nomor HP pasien kosong'], 422);
  }
  // Format Indonesia: mulai dengan 62, hapus 0 di depan
  if (strpos($telp, '62') !== 0) {
    $telp = '62' . ltrim($telp, '0');
  }
  $chatId = $telp . '@c.us';

} catch (Throwable $e) {
  out(['ok'=>false,'error'=>'DB error: '.$e->getMessage()], 500);
}

// ===== Susun pesan =====
$pesan = "Halo {$nama}, obat Anda (Antrian {$no} - {$jenis}) sudah siap diambil di Farmasi RSU Handayani. Terima kasih.";

// ===== Kirim ke WAHA (format yang sama seperti Java: chatId + text + session) =====
$endpoint = $WAHA_URL . '/api/sendText';
$payload = json_encode([
  'chatId'  => $chatId,
  'text'    => $pesan,
  'session' => $WAHA_SESSION,
], JSON_UNESCAPED_UNICODE);

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
  CURLOPT_POSTFIELDS     => $payload,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT        => 15,
  // Jika WAHA pakai sertifikat self-signed, bisa nonaktif sementara (tidak direkomendasi)
  // CURLOPT_SSL_VERIFYPEER => false,
  // CURLOPT_SSL_VERIFYHOST => false,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

// ===== Logging (opsional) =====
try {
  $logDir = __DIR__ . '/../logs';
  if (!is_dir($logDir)) @mkdir($logDir, 0775, true);
  @file_put_contents(
    $logDir . '/send_wa_pasien.log',
    sprintf(
      "[%s] to=%s code=%s err=%s resp=%s payload=%s\n",
      date('c'), $chatId, (string)$httpCode, (string)$err, substr((string)$response,0,500), $payload
    ),
    FILE_APPEND
  );
} catch (\Throwable $e) {
  // abaikan error log
}

// ===== Evaluasi hasil =====
if ($httpCode >= 200 && $httpCode < 300) {
  out([
    'ok'      => true,
    'nomor'   => $telp,
    'chatId'  => $chatId,
    'pesan'   => $pesan,
    'no_rawat'=> $no_rawat,
    'waha'    => ['code'=>$httpCode, 'resp'=>$response]
  ], 200);
}

// Jika WAHA balas error (sering 4xx/5xx), tampilkan detail agar gampang debug
out([
  'ok'    => false,
  'error' => "Gagal kirim WA (HTTP {$httpCode})",
  'detail'=> ['curl_error'=>$err, 'resp'=>$response]
], 502);