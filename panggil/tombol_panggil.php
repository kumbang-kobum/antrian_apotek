<!DOCTYPE html>
<!-- Pembuat Chandra Irawan M.T.I -->
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Panggil Antrian</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(rgba(0,0,50,0.6), rgba(0,0,50,0.6)), url('../assets/img/bg-farmasi.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
    }

    .container {
      display: flex;
      height: 100vh;
      padding: 20px;
      box-sizing: border-box;
    }

    .video-column {
      flex: 2;
      padding-right: 20px;
    }

    .video-column video {
      width: 100%;
      height: 100%;
      border-radius: 15px;
      object-fit: contain;
      background-color: #000;
      box-shadow: 0 0 15px black;
    }

    .antrian-columns {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .antrian-box {
      flex: 1;
      background-color: rgba(0, 0, 70, 0.85);
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 0 10px #000;
    }

    .antrian-box h2 {
      font-size: 24px;
      margin-bottom: 10px;
      color: #00ffff;
    }

    .antrian-number {
      font-size: 60px;
      font-weight: bold;
      color: #ffeb3b;
      margin-bottom: 10px;
    }

    .antrian-name {
      font-size: 20px;
      margin-bottom: 15px;
      color: #ffffff;
    }

    .antrian-box button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #2196f3;
      color: white;
      border: none;
      border-radius: 8px;
      margin: 5px;
      cursor: pointer;
    }

    .antrian-box button:hover {
      background-color: #0b7dda;
    }

    .antrian-list {
      max-height: 150px;
      overflow-y: auto;
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      margin-top: 15px;
      padding: 10px;
      font-size: 14px;
      text-align: left;
    }

    .antrian-list div {
      padding: 5px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .home-button {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #0b7dda;
        }

        .home-button:hover {
            background-color: #0b7dda;
        }
  </style>
</head>
<body>
  <div class="container">
    <div class="antrian-columns">
      <div class="antrian-box">
        <h2>ANTRIAN NON RACIK</h2>
        <div class="antrian-number" id="nonracik_antrian">000</div>
        <div class="antrian-name" id="nonracik_nama">-</div>
        <form id="form-loket" style="margin-bottom: 10px;">
          <!-- <label><input type="radio" name="loket" value="1"> Loket 1</label>
          <label><input type="radio" name="loket" value="2"> Loket 2</label>
          <label><input type="radio" name="loket" value="3"> Loket 3</label>
          <label><input type="radio" name="loket" value="4"> Loket 4</label>
          <label><input type="radio" name="loket" value="5"> Loket 5</label>
          <label><input type="radio" name="loket" value="6"> Loket 6</label> -->
          <label><input type="radio" name="loket" value="5"> Loket 5</label>
          <label><input type="radio" name="loket" value="6"> Loket 6</label>
          <label><input type="radio" name="loket" value="7"> Loket 7</label>
          <label><input type="radio" name="loket" value="8"> Loket 8</label>
          <label><input type="radio" name="loket" value="9"> Loket 9</label>
          <label><input type="radio" name="loket" value="10"> Loket 10</label>
        </form>
        <button onclick="panggil('Non Racik')">Panggil</button>
        <button onclick="panggilUlang('Non Racik')">Ulangi</button>
        <button class="home-button" onclick="window.location.href='../index.php'">🏠 Home</button>
      </div>

      <div class="antrian-box">
        <h2>ANTRIAN RACIK</h2>
        <div class="antrian-number" id="racik_antrian">000</div>
        <div class="antrian-name" id="racik_nama">-</div>
        <button onclick="panggil('Racik')">Panggil</button>
        <button onclick="panggilUlang('Racik')">Ulangi</button>
        <div class="antrian-list" id="list_racik"></div>
      </div>
    </div>
  </div>

  <script src="../assets/js/audio.js"></script>
  <script>
    function loadDaftarAntrian(jenis) {
      fetch('get_daftar_antrian.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'jenis=' + encodeURIComponent(jenis)
      })
      .then(res => res.json())
      .then(data => {
        const idList = jenis === 'Non Racik' ? 'list_nonracik' : 'list_racik';
        const listEl = document.getElementById(idList);
        listEl.innerHTML = "";
        data.forEach((item) => {
          const row = document.createElement("div");
          row.innerText = `${item.no_antrian} - ${item.nama}`;
          listEl.appendChild(row);
        });
      });
    }

    // Jalankan saat load dan interval
    loadDaftarAntrian('Non Racik');
    loadDaftarAntrian('Racik');
    setInterval(() => {
      loadDaftarAntrian('Non Racik');
      loadDaftarAntrian('Racik');
    }, 10000);

    const lastAntrian = {
      "Non Racik": { nomor: "000", nama: "-" },
      "Racik": { nomor: "000", nama: "-" }
    };

    function panggil(jenis) {
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if (!loket) {
        alert("Pilih loket terlebih dahulu!");
        return;
      }

      fetch('get_antrian.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'jenis=' + encodeURIComponent(jenis) + '&loket=' + encodeURIComponent(loket)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'sukses') {
          lastAntrian[jenis] = {
            nomor: data.no_antrian,
            nama: data.nama ?? "-"
          };
          const idPrefix = jenis === 'Non Racik' ? 'nonracik' : 'racik';
          document.getElementById(idPrefix + '_antrian').innerText = data.no_antrian;
          document.getElementById(idPrefix + '_nama').innerText = data.nama ?? "-";
          mainkanAudio(data.no_antrian, jenis, loket);
        } else {
          alert("Tidak ada antrian " + jenis + " tersedia.");
        }
      });
    }

    function panggilUlang(jenis) {
  const antrian = lastAntrian[jenis];
  const loket = document.querySelector('input[name="loket"]:checked')?.value;

  if (!loket) {
    alert("Pilih loket terlebih dahulu!");
    return;
  }

  if (antrian.nomor === "000") {
    alert("Belum ada antrian yang dipanggil untuk " + jenis);
    return;
  }

  // 1. Mainkan audio secara lokal
  mainkanAudio(antrian.nomor, jenis, loket);

  // 2. Kirim ulang ke TV display
  fetch('update_audio.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body:
      'nomor=' + encodeURIComponent(antrian.nomor) +
      '&jenis=' + encodeURIComponent(jenis) +
      '&loket=' + encodeURIComponent(loket)
  });
}
  </script>
  <footer style="text-align:center; padding:10px; background:rgba(0,0,50,0.5); color:#ccc; position:fixed; bottom:0; width:100%;">
    &copy; 2025 Sistem Antrian Apotek | Dibuat oleh Chandra Irawan M.T.I | RS Handayani
  </footer>
</body>
</html>