<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan 
//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
//  Emirza Wira M.T.I yang berbentuk exe
// ambil/cetak_antrian.php
$no_antrian = $_GET['no_antrian'] ?? '000';
$nm_pasien = $_GET['nm_pasien'] ?? '-';
$resep = $_GET['resep'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Antrian</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            text-align: center;
            padding: 50px;
        }
        .ticket {
            border: 1px dashed #000;
            padding: 30px;
            display: inline-block;
        }
        .ticket h1 {
            font-size: 60px;
            margin: 10px 0;
        }
        .ticket p {
            margin: 5px 0;
            font-size: 20px;
        }
        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <p><strong>Nomor Antrian</strong></p>
        <h1><?= htmlspecialchars($no_antrian) ?></h1>
        <p><?= htmlspecialchars($nm_pasien) ?></p>
        <p>Jenis: <?= htmlspecialchars($resep) ?></p>
    </div>
    <br>
    <button onclick="window.print()">Cetak</button>
</body>
</html>