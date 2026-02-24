<?php
include '../config/database.php';
header('Content-Type: application/json');

// Fungsi masking nama pasien
function maskNamaPasien($nama) {
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

if (!in_array($jenis, ['Non Racik', 'Racik'], true)) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Jenis antrian tidak valid']);
    exit;
}

if ($loket !== '' && !preg_match('/^[0-9]{1,2}$/', $loket)) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Loket tidak valid']);
    exit;
}

try {
    $stmt = $pdo->prepare("
      SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep
      FROM antrian_farmasi_rajal a
      JOIN resep_obat r ON a.no_resep = r.no_resep
      JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
      JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
      WHERE a.status='0' AND a.resep=? AND a.tgl_antri=CURDATE()
      ORDER BY a.no_antrian ASC LIMIT 1
    ");
    $stmt->execute([$jenis]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $maskedNama = maskNamaPasien($data['nama']);
        $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='1' WHERE no_resep=?")->execute([$data['no_resep']]);

        $lastAntrianFile = __DIR__ . '/last_antrian.json';
        $lastAntrian = file_exists($lastAntrianFile) ? json_decode(file_get_contents($lastAntrianFile), true) : [];
        $lastAntrian[$jenis] = [
            'nomor' => $data['no_antrian'],
            'nama' => $maskedNama
        ];
        file_put_contents($lastAntrianFile, json_encode($lastAntrian, JSON_PRETTY_PRINT));

        if (!empty($data['no_antrian'])) {
            $lastAudioFile = __DIR__ . '/last_audio.json';
            $audioData = [
                'timestamp' => date('c'),
                'nomor' => $data['no_antrian'],
                'jenis' => $jenis,
                'loket' => $loket
            ];
            file_put_contents($lastAudioFile, json_encode($audioData, JSON_PRETTY_PRINT));
        }

        echo json_encode([
            'status' => 'sukses',
            'no_antrian' => $data['no_antrian'],
            'nama' => $maskedNama
        ]);
    } else {
        echo json_encode(['status' => 'kosong', 'pesan' => 'Tidak ada antrian tersedia']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
}
?>
