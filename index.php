<!-- 
 Pembuat Chandra Irawan M.T.I
 Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
 sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
 bagi yang ingin berdonasi secangkir kopi bisa melalui
 BCA 8110400102 A/N Chandra Irawan 
 ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
 pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
 Emirza Wira M.T.I yang berbentul exe -->
 <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Portal Antrian Apotek</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(to bottom right, #004466, #001f33);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }

    .portal-container {
      max-width: 600px;
      width: 90%;
    }

    h1 {
      font-size: 32px;
      margin-bottom: 40px;
    }

    .btn {
      display: block;
      width: 100%;
      padding: 15px;
      margin: 10px 0;
      font-size: 20px;
      background-color: #2196f3;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      text-decoration: none;
    }

    .btn:hover {
      background-color: #0b7dda;
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 24px;
      }

      .btn {
        font-size: 18px;
        padding: 12px;
      }
    }
  </style>
</head>
<body>
  <div class="portal-container">
    <h1>Selamat Datang di Antrian Apotek</h1>
    <a class="btn" href="ambil/">Ambil Antrian</a>
    <a class="btn" href="panggil/">Tampilkan Antrian</a>
  </div>
</body>
</html>