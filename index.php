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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Antrian Apotek</title>
  <style>
    :root {
      --bg: #747a84;
      --text: #f5f7fa;
      --muted: #d7dde6;
      --card: #565c66;
      --line: rgba(255, 255, 255, 0.18);
      --shadow: 0 14px 30px rgba(0, 0, 0, 0.24);
      --btn-a: #31363f;
      --btn-b: #22262d;
      --btn-border: rgba(255, 255, 255, 0.2);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      color: var(--text);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .portal-container {
      width: min(980px, 100%);
      background: var(--card);
      border: 1px solid var(--line);
      border-radius: 22px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(8px);
      padding: 30px 28px 24px;
    }

    .heading {
      margin-bottom: 24px;
      text-align: center;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .brand img {
      width: 54px;
      height: 54px;
      object-fit: contain;
    }

    .heading h1 {
      margin: 0;
      letter-spacing: 1px;
      font-size: clamp(28px, 3.8vw, 44px);
      line-height: 1;
    }

    .heading p {
      margin: 8px 0 0;
      color: var(--muted);
      font-size: 15px;
    }

    .btn-group {
      display: grid;
      grid-template-columns: repeat(2, minmax(180px, 1fr));
      gap: 14px;
    }

    .btn {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      color: #fff;
      font-size: 17px;
      font-weight: 600;
      padding: 16px 18px;
      border-radius: 14px;
      border: 1px solid var(--btn-border);
      background: linear-gradient(180deg, var(--btn-a), var(--btn-b));
      transition: transform 0.15s ease, box-shadow 0.2s ease;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.28);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 24px rgba(0, 0, 0, 0.3);
    }

    .btn span {
      font-size: 22px;
      line-height: 1;
    }

    footer {
      margin-top: 20px;
      color: var(--muted);
      font-size: 13px;
      text-align: center;
    }

    @media (max-width: 700px) {
      .portal-container {
        padding: 22px 16px 18px;
        border-radius: 18px;
      }

      .btn-group {
        grid-template-columns: 1fr;
      }

      .btn {
        justify-content: flex-start;
      }
    }
  </style>
</head>
<body>
  <div class="portal-container">
    <div class="heading">
      <div class="brand">
        <img src="assets/img/n2Nlogo.png" alt="n2N Logo">
        <h1>n2N-antrian Apotek</h1>
      </div>
      <p>Antrian Apotek Terintegrasi dengan SIMRS Khanza</p>
    </div>

    <div class="btn-group">
      <a class="btn" href="ambil/"><span>🎟️</span>Ambil Antrian</a>
      <a class="btn" href="panggil/"><span>📺</span>Tampilkan Antrian</a>
      <a class="btn" href="panggil/tombol_panggil.php"><span>🔊</span>Panggil Pasien</a>
      <a class="btn" href="laporan/"><span>📊</span>Laporan Harian</a>
    </div>

    <footer>&copy; 2026 n2N-Sistem Antrian Apotek</footer>
  </div>
</body>
</html>
