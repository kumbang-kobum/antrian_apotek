<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/audit.php';
require_once __DIR__ . '/../config/skipped.php';
header('Content-Type: application/json');

function maskNamaPasienRecall($nama) {
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
    $pickedNoResep = null;

    // Ambil antrean terlewati paling lama (FIFO) yang masih status menunggu.
    for ($i = 0; $i < 100; $i++) {
        $candidate = popSkippedNoResep($jenis, $today);
        if ($candidate === null) {
            break;
        }

        $stmt = $pdo->prepare("
          SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep
          FROM antrian_farmasi_rajal a
          JOIN resep_obat r ON a.no_resep = r.no_resep
          JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
          JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
          WHERE a.no_resep=? AND a.resep=? AND a.status='0' AND a.tgl_antri=CURDATE()
          LIMIT 1
        ");
        $stmt->execute([$candidate, $jenis]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $data = $row;
            $pickedNoResep = $candidate;
            break;
        }
    }

    if (!$data) {
        audit_log('antrian.terlewati.kosong', ['jenis' => $jenis, 'loket' => $loket]);
        echo json_encode(['status' => 'kosong', 'pesan' => 'Tidak ada antrian terlewati']);
        exit;
    }

    $maskedNama = maskNamaPasienRecall($data['nama']);
    $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='1' WHERE no_resep=?")->execute([$data['no_resep']]);
    removeSkippedNoResep($jenis, $today, $pickedNoResep);

    $lastAntrianFile = __DIR__ . '/last_antrian.json';
    $lastAntrian = file_exists($lastAntrianFile) ? json_decode(file_get_contents($lastAntrianFile), true) : [];
    $lastAntrian[$jenis] = [
        'nomor' => $data['no_antrian'],
        'nama' => $maskedNama
    ];
    file_put_contents($lastAntrianFile, json_encode($lastAntrian, JSON_PRETTY_PRINT));

    $lastAudioFile = __DIR__ . '/last_audio.json';
    $audioData = [
        'timestamp' => sprintf('%.6f', microtime(true)),
        'nomor' => $data['no_antrian'],
        'jenis' => $jenis,
        'loket' => $loket
    ];
    file_put_contents($lastAudioFile, json_encode($audioData, JSON_PRETTY_PRINT));

    audit_log('antrian.terlewati.sukses', [
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
    audit_log('antrian.terlewati.gagal', ['jenis' => $jenis, 'loket' => $loket, 'error' => $e->getMessage()]);
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
}

