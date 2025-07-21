<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan

//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh


//  Emirza Wira M.T.I yang berbentuk exe
// panggil/tombol_panggil.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nomor' => $_POST['nomor'] ?? '000',
        'jenis' => $_POST['jenis'] ?? '',
        'loket' => $_POST['loket'] ?? ''
    ];

    file_put_contents('last_antrian.json', json_encode($data));
    echo json_encode(['status' => 'sukses']);
    exit;
}

echo json_encode(['status' => 'gagal']);
?>