<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Panggil Antrian</title>
  <style>
    body{margin:0;font-family:"Segoe UI",sans-serif;background:linear-gradient(rgba(0,0,50,.6),rgba(0,0,50,.6)),url('../assets/img/bg-farmasi.jpg') no-repeat center/cover fixed;color:#fff}
    .container{display:flex;height:100vh;padding:20px;box-sizing:border-box}
    .video-column{flex:2;padding-right:20px}
    .video-column video{width:100%;height:100%;border-radius:15px;object-fit:contain;background:#000;box-shadow:0 0 15px #000}
    .antrian-columns{flex:1;display:flex;flex-direction:column;gap:20px}
    .antrian-box{flex:1;background:rgba(0,0,70,.85);border-radius:15px;padding:20px;text-align:center;box-shadow:0 0 10px #000}
    .antrian-box h2{font-size:24px;margin-bottom:10px;color:#0ff}
    .antrian-number{font-size:60px;font-weight:bold;color:#ffeb3b;margin-bottom:10px}
    .antrian-name{font-size:20px;margin-bottom:15px}
    .antrian-box button{padding:10px 20px;font-size:16px;background:#2196f3;color:#fff;border:none;border-radius:8px;margin:5px;cursor:pointer}
    .antrian-box button:hover{background:#0b7dda}
    .antrian-list{max-height:150px;overflow-y:auto;background:rgba(255,255,255,.05);border-radius:10px;margin-top:15px;padding:10px;font-size:14px;text-align:left}
    .antrian-list div{padding:5px;border-bottom:1px solid rgba(255,255,255,.1)}
    .home-button{margin-bottom:20px;padding:10px;background:#0b7dda}
  </style>
</head>
<body>
  <div class="container">
    <div class="antrian-columns">
      <!-- NON RACIK -->
      <div class="antrian-box">
        <h2>ANTRIAN NON RACIK</h2>
        <div class="antrian-number" id="nonracik_antrian">000</div>
        <div class="antrian-name" id="nonracik_nama">-</div>

        <form id="form-loket" style="margin-bottom: 10px;">
          <label><input type="radio" name="loket" value="5"> Loket 5</label>
          <label><input type="radio" name="loket" value="6"> Loket 6</label>
          <label><input type="radio" name="loket" value="7"> Loket 7</label>
          <label><input type="radio" name="loket" value="8"> Loket 8</label>
          <label><input type="radio" name="loket" value="9"> Loket 9</label>
          <label><input type="radio" name="loket" value="10"> Loket 10</label>
        </form>

        <button onclick="panggil('Non Racik')">Panggil</button>
        <button onclick="panggilUlang('Non Racik')">Ulangi</button>
        <button onclick="kirimWA('Non Racik')">Kirim WA</button>
        <button class="home-button" onclick="window.location.href='../index.php'">🏠 Home</button>

        <div class="antrian-list" id="list_nonracik"></div>
      </div>

      <!-- RACIK -->
      <div class="antrian-box">
        <h2>ANTRIAN RACIK</h2>
        <div class="antrian-number" id="racik_antrian">000</div>
        <div class="antrian-name" id="racik_nama">-</div>

        <button onclick="panggil('Racik')">Panggil</button>
        <button onclick="panggilUlang('Racik')">Ulangi</button>
        <button onclick="kirimWA('Racik')">Kirim WA</button>

        <div class="antrian-list" id="list_racik"></div>
      </div>
    </div>
  </div>

  <script src="../assets/js/audio.js"></script>
  <script>
    // ===== STATE TERKINI =====
    const lastAntrian = {
      "Non Racik": { nomor: "000", nama: "-" },
      "Racik":     { nomor: "000", nama: "-" }
    };

    // ===== LIST ANTRIAN =====
    function loadDaftarAntrian(jenis){
      fetch('get_daftar_antrian.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'jenis='+encodeURIComponent(jenis)
      })
      .then(r=>r.json())
      .then(data=>{
        const idList = (jenis==='Non Racik')?'list_nonracik':'list_racik';
        const el = document.getElementById(idList);
        el.innerHTML = '';
        data.forEach(item=>{
          const row = document.createElement('div');
          row.innerText = `${item.no_antrian} - ${item.nama}`;
          el.appendChild(row);
        });
      })
      .catch(()=>{});
    }

    // initial & refresh tiap 10 dtk
    loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
    setInterval(()=>{ loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik'); }, 10000);

    // ===== PANGGIL & ULANGI =====
    function panggil(jenis){
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if(!loket){ alert('Pilih loket terlebih dahulu!'); return; }

      fetch('get_antrian.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)
      })
      .then(r=>r.json())
      .then(d=>{
        if(d.status==='sukses'){
          lastAntrian[jenis] = { nomor:d.no_antrian, nama:(d.nama||'-') };
          const idPrefix = (jenis==='Non Racik')?'nonracik':'racik';
          document.getElementById(idPrefix+'_antrian').innerText = d.no_antrian;
          document.getElementById(idPrefix+'_nama').innerText    = d.nama || '-';
          mainkanAudio(d.no_antrian, jenis, loket);
        }else{
          alert('Tidak ada antrian '+jenis+' tersedia.');
        }
      });
    }

    function panggilUlang(jenis){
      const antri = lastAntrian[jenis];
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if(!loket){ alert('Pilih loket terlebih dahulu!'); return; }
      if(antri.nomor==='000'){ alert('Belum ada antrian yang dipanggil untuk '+jenis); return; }

      mainkanAudio(antri.nomor, jenis, loket);
      fetch('update_audio.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'nomor='+encodeURIComponent(antri.nomor)+'&jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)
      });
    }

    // ===== KIRIM WA =====
    async function kirimWA(jenis){
      const no = (lastAntrian[jenis]?.nomor || '').trim();
      if(!no){ alert('Belum ada nomor antrian terakhir untuk '+jenis); return; }

      try{
        const res = await fetch('send_wa_pasien.php', {
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:'jenis='+encodeURIComponent(jenis)+'&no_antrian='+encodeURIComponent(no)
        });

        const txt = await res.text();
        let data;
        try{ data = JSON.parse(txt); }
        catch(e){ alert('Response bukan JSON:\n'+txt); return; }

        if(data.ok){
          alert('✅ WA terkirim ke '+data.nomor);
        }else{
          alert('❌ '+(data.error||'Gagal kirim WA'));
          console.error('Detail:', data);
        }
      }catch(err){
        alert('❌ Gagal kirim WA: '+err);
      }
    }
  </script>

  <footer style="text-align:center;padding:10px;background:rgba(0,0,50,.5);color:#ccc;position:fixed;bottom:0;width:100%;">
    &copy; 2025 Sistem Antrian Apotek | Dibuat oleh Chandra Irawan M.T.I | RS Handayani
  </footer>
</body>
</html>