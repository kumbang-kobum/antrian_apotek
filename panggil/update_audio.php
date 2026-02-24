<?php
// menerima ulangi panggilan dari tombol_panggil.php
include '../config/audit.php';
header('Content-Type: application/json');

$nomor = trim($_POST['nomor'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');
$loket = trim($_POST['loket'] ?? '');

if (!in_array($jenis, ['Non Racik', 'Racik'], true)) {
    audit_log('antrian.ulangi.validasi_gagal', [
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Jenis tidak valid'
    ]);
    echo json_encode(['status' => 'error', 'message' => 'Jenis tidak valid']);
    exit;
}

if (!preg_match('/^[0-9]{1,5}$/', $nomor)) {
    audit_log('antrian.ulangi.validasi_gagal', [
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Nomor antrian tidak valid'
    ]);
    echo json_encode(['status' => 'error', 'message' => 'Nomor antrian tidak valid']);
    exit;
}

if (!preg_match('/^[0-9]{1,2}$/', $loket)) {
    audit_log('antrian.ulangi.validasi_gagal', [
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Loket tidak valid'
    ]);
    echo json_encode(['status' => 'error', 'message' => 'Loket tidak valid']);
    exit;
}

if ($nomor !== '' && $jenis !== '' && $loket !== '') {
    $data = [
        'timestamp' => date('c'),
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket
    ];
    $written = file_put_contents(__DIR__ . '/last_audio.json', json_encode($data, JSON_PRETTY_PRINT));
    if ($written === false) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menulis file audio']);
        audit_log('antrian.ulangi.gagal', [
            'nomor' => $nomor,
            'jenis' => $jenis,
            'loket' => $loket,
            'pesan' => 'Gagal menulis file audio'
        ]);
    } else {
        echo json_encode(['status' => 'ok']);
        audit_log('antrian.ulangi.sukses', [
            'nomor' => $nomor,
            'jenis' => $jenis,
            'loket' => $loket
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    audit_log('antrian.ulangi.validasi_gagal', [
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket,
        'pesan' => 'Data tidak lengkap'
    ]);
}
?>
