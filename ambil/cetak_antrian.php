<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan
//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh
//  Emirza Wira M.T.I yang berbentuk exe
$no_antrian = $_GET['no_antrian'] ?? '000';
$nm_pasien = $_GET['nm_pasien'] ?? '-';
$resep = $_GET['resep'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Antrian</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: grid;
            place-items: center;
            background: #0a1933;
            color: #fff;
            padding: 20px;
        }

        .ticket {
            width: min(330px, 100%);
            border: 2px dashed rgba(255,255,255,.4);
            border-radius: 14px;
            background: rgba(8, 25, 52, .86);
            text-align: center;
            padding: 24px;
            box-shadow: 0 14px 26px rgba(0,0,0,.3);
        }

        .ticket h1 {
            font-size: 64px;
            margin: 8px 0;
            color: #ffd03a;
            line-height: 1;
        }

        .ticket p { margin: 6px 0; font-size: 18px; }

        button {
            margin-top: 14px;
            border: 0;
            border-radius: 8px;
            background: #1f9ff4;
            color: #fff;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        @media print {
            body { background: #fff; color: #000; padding: 0; }
            .ticket {
                width: 75mm;
                border-color: #000;
                box-shadow: none;
                background: #fff;
                color: #000;
            }
            button { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <p><strong>Nomor Antrian</strong></p>
        <h1><?= htmlspecialchars($no_antrian) ?></h1>
        <p><?= htmlspecialchars($nm_pasien) ?></p>
        <p>Jenis: <?= htmlspecialchars($resep) ?></p>
        <button onclick="window.print()">Cetak</button>
    </div>
</body>
</html>
