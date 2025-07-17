<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan 
//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
//  Emirza Wira M.T.I yang berbentul exe
include '../config/database.php';

$stmt = $pdo->prepare("
  SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep 
  FROM antrian_farmasi_rajal a
  JOIN resep_obat r ON a.no_resep = r.no_resep
  JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
  JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
  WHERE a.status='0' AND a.resep=? AND a.tgl_antri=CURDATE()
  ORDER BY a.no_antrian ASC LIMIT 1
");
$stmt->execute([$_POST['jenis']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='1' WHERE no_resep=?")->execute([$data['no_resep']]);
    echo json_encode(['status' => 'sukses', 'no_antrian' => $data['no_antrian'], 'nama' => $data['nama']]);
} else {
    echo json_encode(['status' => 'kosong']);
}
// Tambahan simpan ke last_antrian.json
$jenis = $_POST['jenis'];
$lastFile = __DIR__ . '/last_antrian.json';

$lastData = [];
if (file_exists($lastFile)) {
    $lastData = json_decode(file_get_contents($lastFile), true);
}
$lastData[$jenis] = [
    'nomor' => $data['no_antrian'],
    'nama'  => $data['nama']
];
file_put_contents($lastFile, json_encode($lastData));
?>