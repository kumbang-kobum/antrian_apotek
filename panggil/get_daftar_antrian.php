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

$jenis = $_POST['jenis'] ?? 'Non Racik';

$stmt = $pdo->prepare("SELECT a.no_antrian, p.nm_pasien AS nama
FROM antrian_farmasi_rajal a
JOIN resep_obat r ON a.no_resep = r.no_resep
JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
WHERE a.status = '0' AND a.resep = ? AND a.tgl_antri = CURDATE()
ORDER BY a.no_antrian ASC");

$stmt->execute([$jenis]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>