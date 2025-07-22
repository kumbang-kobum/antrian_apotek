<?php
include '../config/database.php';

$jenis = $_POST['jenis'] ?? '';
$loket = $_POST['loket'] ?? '';

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
    // Update status jadi '1' (dipanggil)
    $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='1' WHERE no_resep=?")->execute([$data['no_resep']]);

    // Simpan ke last_antrian.json
    $lastFile = __DIR__ . '/last_antrian.json';
    $lastData = file_exists($lastFile) ? json_decode(file_get_contents($lastFile), true) : [];
    $lastData[$jenis] = [
        'nomor' => $data['no_antrian'],
        'nama'  => $data['nama']
    ];
    file_put_contents($lastFile, json_encode($lastData, JSON_PRETTY_PRINT));

    // Simpan ke last_audio.json (untuk TV Android)
    if (!empty($data['no_antrian'])) {
  // Simpan ke last_antrian.json
  $lastAntrianFile = __DIR__ . '/last_antrian.json';
  $lastAntrian = file_exists($lastAntrianFile) ? json_decode(file_get_contents($lastAntrianFile), true) : [];
  $lastAntrian[$jenis] = [
    'nomor' => $data['no_antrian'],
    'nama' => $data['nama']
  ];
  file_put_contents($lastAntrianFile, json_encode($lastAntrian, JSON_PRETTY_PRINT));

  // Simpan ke last_audio.json
  $lastAudioFile = __DIR__ . '/last_audio.json';
  $audioData = [
    'timestamp' => date('c'),
    'nomor' => $data['no_antrian'],
    'jenis' => $jenis,
    'loket' => $loket
  ];
  file_put_contents($lastAudioFile, json_encode($audioData, JSON_PRETTY_PRINT));
}

    // Kirim respons sukses
    echo json_encode([
        'status' => 'sukses',
        'no_antrian' => $data['no_antrian'],
        'nama' => $data['nama']
    ]);
} else {
    // Tidak ada antrian tersedia
    echo json_encode(['status' => 'kosong']);
}
?>