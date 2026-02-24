<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/audit.php';
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
    audit_log('antrian.panggil.validasi_gagal', [
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Jenis antrian tidak valid'
    ]);
    echo json_encode(['status' => 'gagal', 'pesan' => 'Jenis antrian tidak valid']);
    exit;
}

if ($loket !== '' && !preg_match('/^[0-9]{1,2}$/', $loket)) {
    audit_log('antrian.panggil.validasi_gagal', [
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Loket tidak valid'
    ]);
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
        $writtenLast = file_put_contents($lastAntrianFile, json_encode($lastAntrian, JSON_PRETTY_PRINT));

        if (!empty($data['no_antrian'])) {
            $lastAudioFile = __DIR__ . '/last_audio.json';
            $audioData = [
                'timestamp' => sprintf('%.6f', microtime(true)),
                'nomor' => $data['no_antrian'],
                'jenis' => $jenis,
                'loket' => $loket
            ];
            $writtenAudio = file_put_contents($lastAudioFile, json_encode($audioData, JSON_PRETTY_PRINT));
            if ($writtenAudio === false) {
                audit_log('antrian.panggil.write_audio_gagal', [
                    'jenis' => $jenis,
                    'loket' => $loket,
                    'no_antrian' => $data['no_antrian']
                ]);
            }
        }

        if ($writtenLast === false) {
            audit_log('antrian.panggil.write_last_gagal', [
                'jenis' => $jenis,
                'loket' => $loket,
                'no_antrian' => $data['no_antrian']
            ]);
        }

        echo json_encode([
            'status' => 'sukses',
            'no_antrian' => $data['no_antrian'],
            'nama' => $maskedNama
        ]);
        audit_log('antrian.panggil.sukses', [
            'jenis' => $jenis,
            'loket' => $loket,
            'no_resep' => $data['no_resep'],
            'no_antrian' => $data['no_antrian'],
            'nama_masked' => $maskedNama
        ]);
    } else {
        echo json_encode(['status' => 'kosong', 'pesan' => 'Tidak ada antrian tersedia']);
        audit_log('antrian.panggil.kosong', [
            'jenis' => $jenis,
            'loket' => $loket
        ]);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
    audit_log('antrian.panggil.gagal', [
        'jenis' => $jenis,
        'loket' => $loket,
        'error' => $e->getMessage()
    ]);
}
?>
