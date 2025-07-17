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
      object-fit: contain; /* sebelumnya cover */
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
  </style>
</head>
<body>
  <div class="container">
    <!-- KIRI: VIDEO -->
    <div class="video-column">
      <video autoplay muted loop id="edukasiVideo">
        <source src="../assets/video/edukasi.mp4" type="video/mp4">
        Video tidak tersedia.
      </video>
    </div>

    <!-- KANAN: ANTRIAN NON & RACIK -->
    <div class="antrian-columns">
      <!-- Non Racikan -->
      <div class="antrian-box">
        <h2>ANTRIAN NON RACIK</h2>
        <div class="antrian-number" id="nonracik_antrian">000</div>
        <div class="antrian-name" id="nonracik_nama">-</div>
        <button onclick="panggil('Non Racik')">Panggil</button>
        <button onclick="panggilUlang('Non Racik')">Ulangi</button>
        <div class="antrian-list" id="list_nonracik"></div>
      </div>

      <!-- Racikan -->
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
    const vid = document.getElementById("edukasiVideo");
    vid.addEventListener("ended", function () {
    this.currentTime = 0;
    this.play();
  });

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

// Jalankan otomatis setiap 10 detik
setInterval(() => {
  loadDaftarAntrian('Non Racik');
  loadDaftarAntrian('Racik');
}, 10000);

// Panggil sekali saat pertama load
loadDaftarAntrian('Non Racik');
loadDaftarAntrian('Racik');

    const lastAntrian = {
      "Non Racik": { nomor: "000", nama: "-" },
      "Racik": { nomor: "000", nama: "-" }
    };

    function panggil(jenis) {
      fetch('get_antrian.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'jenis=' + encodeURIComponent(jenis)
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
          document.getElementById(idPrefix + '_nama').innerText = lastAntrian[jenis].nama;
          mainkanAudio(data.no_antrian, jenis);
        } else {
          alert("Tidak ada antrian " + jenis + " tersedia.");
        }
      });
    }

    function panggilUlang(jenis) {
      const antrian = lastAntrian[jenis];
      if (antrian.nomor === "000") {
        alert("Belum ada antrian yang dipanggil untuk " + jenis);
        return;
      }
      mainkanAudio(antrian.nomor, jenis);
    }
  </script>
  <footer style="text-align:center; padding:10px; background:rgba(0,0,50,0.5); color:#ccc; position:fixed; bottom:0; width:100%;">
  &copy; 2025 Sistem Antrian Apotek | Dibuat oleh Chandra Irawan M.T.I
</footer>
</body>
</html>