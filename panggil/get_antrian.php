<!-- 
 Pembuat Chandra Irawan M.T.I
 Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
 sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
 bagi yang ingin berdonasi secangkir kopi bisa melalui
 BCA 8110400102 A/N Chandra Irawan 
 ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
 pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
 Emirza Wira M.T.I yang berbentul exe -->
<?php
include '../config/database.php';

$stmt = $pdo->prepare("SELECT * FROM antrian_farmasi_rajal WHERE status='0' AND resep=? AND tgl_antri=CURDATE() ORDER BY no_antrian ASC LIMIT 1");
$stmt->execute([$_POST['jenis']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $pdo->prepare("UPDATE antrian_farmasi_rajal SET status='1' WHERE no_resep=?")->execute([$data['no_resep']]);
    echo json_encode(['status' => 'sukses', 'no_antrian' => $data['no_antrian']]);
} else {
    echo json_encode(['status' => 'kosong']);
}
?>