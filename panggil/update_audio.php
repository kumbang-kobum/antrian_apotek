<?php
// menerima ulangi panggilan dari tombol_panggil.php
$nomor = $_POST['nomor'] ?? '';
$jenis = $_POST['jenis'] ?? '';
$loket = $_POST['loket'] ?? '';

if ($nomor && $jenis && $loket) {
    $data = [
        'timestamp' => date('c'),
        'nomor' => $nomor,
        'jenis' => $jenis,
        'loket' => $loket
    ];
    file_put_contents(__DIR__ . '/last_audio.json', json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
}
?>