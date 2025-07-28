<!DOCTYPE html>
<!-- Pembuat Chandra Irawan M.T.I
 Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
 sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
 bagi yang ingin berdonasi secangkir kopi bisa melalui
 BCA 8110400102 A/N Chandra Irawan 
 ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
 pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
 Emirza Wira M.T.I yang berbentuk exe -->
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
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      text-align: center;
    }

    .portal-container {
      max-width: 700px;
      width: 100%;
      padding: 20px;
    }

    h1 {
      font-size: 32px;
      margin-bottom: 40px;
    }

    .btn-group {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .btn {
      flex: 1 1 200px;
      padding: 20px;
      font-size: 18px;
      background-color: #2196f3;
      color: white;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #0b7dda;
    }

    .btn span {
      display: block;
      font-size: 32px;
      margin-bottom: 8px;
    }

    footer {
      text-align: center;
      padding: 10px;
      background: rgba(0,0,50,0.5);
      color: #ccc;
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    @media (max-width: 600px) {
      .btn-group {
        flex-direction: column;
        gap: 12px;
      }

      .btn span {
        font-size: 28px;
      }
    }
  </style>
</head>
<body>
  <div class="portal-container">
    <h1>ALNAIRA</h1>
    <p>Aplikasi Loket Naikkan Respon Antrian</p>
    <p>RS Handayani</p>
    <div class="btn-group">
      <a class="btn" href="ambil/">
        <span>🎟️</span>Ambil Antrian
      </a>
      <a class="btn" href="panggil/">
        <span>💻</span>Tampilkan Antrian
      </a>
      <a class="btn" href="panggil/tombol_panggil.php">
        <span>🔊</span>Panggil
      </a>
    </div>
  </div>

  <footer>
    &copy; 2025 Sistem Antrian Apotek | Dibuat oleh Chandra Irawan M.T.I | RS Handayani
  </footer>
</body>
</html>