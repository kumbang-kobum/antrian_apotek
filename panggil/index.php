<!DOCTYPE html>
<!-- Pembuat Chandra Irawan M.T.I
 Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
 sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
 bagi yang ingin berdonasi secangkir kopi bisa melalui
 BCA 8110400102 A/N Chandra Irawan
 ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
 pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh
 Emirza Wira M.T.I yang berbentul exe -->
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panggil Antrian</title>
  <style>
    :root {
      --bg: #747a84;
      --panel: #575d67;
      --line: rgba(255, 255, 255, 0.17);
      --text: #f4f7fb;
      --muted: #d8dee8;
      --primary: #2a2e35;
      --warn: #ffd35b;
      --shadow: 0 14px 30px rgba(0, 0, 0, .26);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      color: var(--text);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg);
      min-height: 100vh;
    }

    .container {
      display: grid;
      grid-template-columns: 2.1fr 1fr;
      gap: 16px;
      height: 100vh;
      padding: 16px;
    }

    .video-column {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 18px;
      overflow: hidden;
      box-shadow: var(--shadow);
      min-height: 0;
    }

    .video-column video {
      width: 100%;
      height: 100%;
      object-fit: contain;
      background: #000;
    }

    .antrian-columns {
      display: grid;
      grid-template-rows: 1fr 1fr;
      gap: 16px;
      min-height: 0;
    }

    .antrian-box {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 18px;
      text-align: center;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      min-height: 0;
    }

    .antrian-box h2 {
      margin: 0 0 8px;
      font-size: clamp(18px, 2.1vw, 24px);
      color: #7cd0ff;
    }

    .antrian-number {
      font-size: clamp(48px, 6vw, 66px);
      font-weight: 800;
      line-height: 1;
      color: var(--warn);
      margin-bottom: 8px;
    }

    .antrian-name {
      font-size: clamp(16px, 2vw, 20px);
      margin-bottom: 12px;
      min-height: 28px;
    }

    .antrian-list {
      margin-top: auto;
      min-height: 0;
      max-height: 38%;
      overflow-y: auto;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 10px;
      padding: 8px;
      font-size: 14px;
      text-align: left;
    }

    .antrian-list div {
      padding: 6px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    #btnSuara {
      position: fixed;
      top: 14px;
      right: 14px;
      padding: 10px 14px;
      border: 0;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(180deg, #2d323a, #1f2329);
      cursor: pointer;
      z-index: 1000;
      box-shadow: 0 10px 20px rgba(0,0,0,.28);
    }

    .footer {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      text-align: center;
      color: var(--muted);
      font-size: 12px;
      background: #4e545e;
      border-top: 1px solid rgba(255,255,255,.1);
      padding: 8px 12px;
    }

    @media (max-width: 980px) {
      .container {
        grid-template-columns: 1fr;
        height: auto;
        min-height: 100vh;
        padding-bottom: 46px;
      }

      .video-column {
        height: 36vh;
      }

      .antrian-columns {
        grid-template-rows: auto;
      }

      .antrian-list {
        max-height: 160px;
      }
    }
  </style>
</head>
<body>
  <button id="btnSuara" onclick="aktifkanSuara()">🔊 Aktifkan Suara</button>

  <div class="container">
    <div class="video-column">
      <video autoplay muted loop id="edukasiVideo">
        <source src="../assets/video/edukasi.mp4" type="video/mp4">
        Video tidak tersedia.
      </video>
    </div>

    <div class="antrian-columns">
      <div class="antrian-box">
        <h2>ANTRIAN NON RACIK</h2>
        <div class="antrian-number" id="nonracik_antrian">000</div>
        <div class="antrian-name" id="nonracik_nama">-</div>
        <div class="antrian-list" id="list_nonracik"></div>
      </div>

      <div class="antrian-box">
        <h2>ANTRIAN RACIK</h2>
        <div class="antrian-number" id="racik_antrian">000</div>
        <div class="antrian-name" id="racik_nama">-</div>
        <div class="antrian-list" id="list_racik"></div>
      </div>
    </div>
  </div>

  <script src="../assets/js/audio.js?v=20260224"></script>
  <script>
    function aktifkanSuara() {
      aktifkanAudioOutput().then((ok) => {
        if (ok) {
          console.log("Autoplay suara diaktifkan.");
          document.getElementById("btnSuara").style.display = "none";
        } else {
          alert("Klik sekali lagi untuk mengaktifkan suara.");
        }
      });
    }

    document.addEventListener('click', () => {
      aktifkanAudioOutput().then((ok) => {
        if (ok) {
          const btn = document.getElementById("btnSuara");
          if (btn) btn.style.display = "none";
        }
      });
    }, { once: true });

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

    function loadLastAntrian() {
      fetch('get_last_antrian.php')
        .then(res => res.json())
        .then(data => {
          if (data["Non Racik"]) {
            document.getElementById("nonracik_antrian").innerText = data["Non Racik"].nomor;
            document.getElementById("nonracik_nama").innerText = data["Non Racik"].nama;
          }
          if (data["Racik"]) {
            document.getElementById("racik_antrian").innerText = data["Racik"].nomor;
            document.getElementById("racik_nama").innerText = data["Racik"].nama;
          }
        });
    }

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

    setInterval(() => {
      loadDaftarAntrian('Non Racik');
      loadDaftarAntrian('Racik');
      loadLastAntrian();
    }, 10000);

    loadDaftarAntrian('Non Racik');
    loadDaftarAntrian('Racik');
    loadLastAntrian();

    let lastAudioKey = null;
    let audioStateInitialized = false;

    setInterval(() => {
      fetch('get_last_audio.php?_=' + Date.now(), { cache: 'no-store' })
        .then(res => res.json())
        .then(data => {
          if (!data.nomor || !data.jenis) return;

          const incomingKey = [
            data.timestamp || '',
            data.nomor || '',
            data.jenis || '',
            data.loket || ''
          ].join('|');

          if (!audioStateInitialized) {
            lastAudioKey = incomingKey;
            audioStateInitialized = true;
            return;
          }

          if (incomingKey !== lastAudioKey) {
            lastAudioKey = incomingKey;
            mainkanAudio(data.nomor, data.jenis, data.loket);
          }
        })
        .catch(err => {
          console.error("Gagal membaca last_audio.json:", err);
        });
    }, 3000);
  </script>

  <div class="footer">&copy; 2025 Sistem Antrian Apotek | Dibuat oleh Chandra Irawan M.T.I | RS Handayani</div>
</body>
</html>
