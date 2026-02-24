<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/audit.php';
require_once __DIR__ . '/../config/skipped.php';
header('Content-Type: application/json');

function maskNamaPasienSkip($nama) {
    $parts = explode(' ', $nama);
    $masked = [];
    foreach ($parts as $part) {
        if (strlen($part) <= 2) {
            $masked[] = $part;
        } else {
            $masked[] = substr($part, 0, 2) . str_repeat('x', strlen($part) - 2);
        }
    }
    return implode(' ', $masked);
}

$jenis = trim($_POST['jenis'] ?? '');
$loket = trim($_POST['loket'] ?? '');
$noAntrian = trim($_POST['no_antrian'] ?? '');
$today = date('Y-m-d');

if (!in_array($jenis, ['Non Racik', 'Racik'], true)) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Jenis antrian tidak valid']);
    exit;
}

if ($loket !== '' && !preg_match('/^[0-9]{1,2}$/', $loket)) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Loket tidak valid']);
    exit;
}

try {
    $data = null;
    if ($noAntrian !== '') {
        // Mode utama: lewati nomor yang terakhir dipanggil (no-show).
        $stmt = $pdo->prepare("
          SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep, a.status
          FROM antrian_farmasi_rajal a
          JOIN resep_obat r ON a.no_resep = r.no_resep
          JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
          JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
          WHERE a.no_antrian=? AND a.resep=? AND a.tgl_antri=CURDATE()
          LIMIT 1
        ");
        $stmt->execute([$noAntrian, $jenis]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            audit_log('antrian.lewati.tidak_ditemukan', ['jenis' => $jenis, 'loket' => $loket, 'no_antrian' => $noAntrian]);
            echo json_encode(['status' => 'gagal', 'pesan' => 'Nomor antrian tidak ditemukan untuk hari ini']);
            exit;
        }

        // Jika sudah sempat ditandai terpanggil, kembalikan ke status tunggu agar bisa dipanggil ulang via menu terlewati.
        if ((string) $data['status'] === '1') {
            $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='0' WHERE no_resep=?")->execute([$data['no_resep']]);
        }
    } else {
        // Fallback kompatibilitas: lewati antrean tunggu terdepan.
        $skipped = getSkippedList($jenis, $today);
        $sql = "
          SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep
          FROM antrian_farmasi_rajal a
          JOIN resep_obat r ON a.no_resep = r.no_resep
          JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
          JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
          WHERE a.status='0' AND a.resep=? AND a.tgl_antri=CURDATE()
        ";
        $params = [$jenis];
        if (!empty($skipped)) {
            $in = implode(',', array_fill(0, count($skipped), '?'));
            $sql .= " AND a.no_resep NOT IN ($in)";
            $params = array_merge($params, $skipped);
        }
        $sql .= " ORDER BY a.no_antrian ASC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$data) {
        audit_log('antrian.lewati.kosong', ['jenis' => $jenis, 'loket' => $loket]);
        echo json_encode(['status' => 'kosong', 'pesan' => 'Tidak ada antrian untuk dilewati']);
        exit;
    }

    addSkippedNoResep($jenis, $today, $data['no_resep']);
    $maskedNama = maskNamaPasienSkip($data['nama']);
    audit_log('antrian.lewati.sukses', [
        'jenis' => $jenis,
        'loket' => $loket,
        'no_resep' => $data['no_resep'],
        'no_antrian' => $data['no_antrian']
    ]);

    echo json_encode([
        'status' => 'sukses',
        'no_antrian' => $data['no_antrian'],
        'nama' => $maskedNama
    ]);
} catch (Throwable $e) {
    audit_log('antrian.lewati.gagal', ['jenis' => $jenis, 'loket' => $loket, 'error' => $e->getMessage()]);
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
}
