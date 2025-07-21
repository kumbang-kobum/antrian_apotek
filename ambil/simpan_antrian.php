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

$sql = "INSERT INTO antrian_farmasi_rajal (no_rawat, no_resep, no_antrian, status, tgl_antri, resep)
        VALUES (?, ?, ?, '0', CURDATE(), ?)";

$stmt = $pdo->prepare($sql);
$sukses = $stmt->execute([
    $_POST['no_rawat'],
    $_POST['no_resep'],
    $_POST['no_antrian'],
    $_POST['resep']
]);

echo json_encode(['status' => $sukses ? 'sukses' : 'gagal']);
?>