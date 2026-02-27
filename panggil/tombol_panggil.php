<!DOCTYPE html>
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
      --line: rgba(255,255,255,.18);
      --text: #f4f7fb;
      --muted: #d8dee8;
      --primary: #31363f;
      --primary-2: #23272f;
      --soft: rgba(255,255,255,.10);
      --warning: #b7861a;
      --danger: #b74646;
      --success: #2a8a62;
      --shadow: 0 14px 30px rgba(0, 0, 0, .26);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
      color: var(--text);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg);
      padding: 16px;
    }

    .container {
      max-width: 1260px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      padding-bottom: 52px;
    }

    .antrian-box {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 16px;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      min-height: 640px;
    }

    .antrian-box h2 {
      margin: 0 0 8px;
      color: #7cd3ff;
      text-align: center;
      font-size: 25px;
    }

    .antrian-number {
      text-align: center;
      font-size: 68px;
      line-height: 1;
      font-weight: 800;
      color: #ffd33b;
    }

    .antrian-name {
      text-align: center;
      font-size: 20px;
      margin-top: 8px;
      min-height: 30px;
    }

    .loket-wrap {
      margin-top: 12px;
      padding: 12px;
      border-radius: 12px;
      background: var(--soft);
      border: 1px solid rgba(255,255,255,.12);
    }

    .loket-title {
      margin: 0 0 8px;
      color: var(--muted);
      font-size: 14px;
    }

    .loket-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(80px, 1fr));
      gap: 8px;
    }

    .loket-grid label {
      font-size: 14px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 8px;
      padding: 6px 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-grid {
      margin-top: 12px;
      display: grid;
      grid-template-columns: repeat(2, minmax(150px, 1fr));
      gap: 8px;
    }

    .btn {
      border: 0;
      border-radius: 10px;
      padding: 11px 10px;
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      background: linear-gradient(180deg, var(--primary), var(--primary-2));
    }

    .btn-soft { background: linear-gradient(180deg, #4b5563, #343b46); }
    .btn-warning { background: linear-gradient(180deg, #d8a93c, var(--warning)); }
    .btn-success { background: linear-gradient(180deg, #3ca67b, var(--success)); }
    .btn-danger { background: linear-gradient(180deg, #d96767, var(--danger)); }

    .full { grid-column: 1 / -1; }

    .list-title {
      margin-top: 12px;
      font-size: 13px;
      font-weight: 700;
      color: #b4cff0;
      text-transform: uppercase;
      letter-spacing: .3px;
    }

    .antrian-list {
      margin-top: 6px;
      max-height: 160px;
      overflow-y: auto;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 10px;
      padding: 8px;
      font-size: 14px;
      text-align: left;
    }

    .antrian-list div {
      padding: 6px;
      border-bottom: 1px solid rgba(255,255,255,.1);
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
      .container { grid-template-columns: 1fr; }
      .antrian-box { min-height: auto; }
      .antrian-number { font-size: 56px; }
    }

    @media (max-width: 520px) {
      .loket-grid { grid-template-columns: repeat(2, minmax(80px, 1fr)); }
      .btn-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="antrian-box">
      <h2>ANTRIAN NON RACIK</h2>
      <div class="antrian-number" id="nonracik_antrian">000</div>
      <div class="antrian-name" id="nonracik_nama">-</div>

      <div class="loket-wrap">
        <div class="loket-title">Pilih Loket</div>
        <form id="form-loket">
          <div class="loket-grid">
            <label><input type="radio" name="loket" value="5"> Loket 5</label>
            <label><input type="radio" name="loket" value="6"> Loket 6</label>
            <label><input type="radio" name="loket" value="7"> Loket 7</label>
            <label><input type="radio" name="loket" value="8"> Loket 8</label>
            <label><input type="radio" name="loket" value="9"> Loket 9</label>
            <label><input type="radio" name="loket" value="10"> Loket 10</label>
          </div>
        </form>
      </div>

      <div class="btn-grid">
        <button class="btn" onclick="panggil('Non Racik')">Panggil</button>
        <button class="btn btn-soft" onclick="panggilUlang('Non Racik')">Ulangi</button>
        <button class="btn btn-warning" onclick="lewati('Non Racik')">Lewati</button>
        <button class="btn btn-success" onclick="panggilTerlewati('Non Racik')">Panggil Terlewati</button>
        <button class="btn btn-soft" onclick="kirimWA('Non Racik')">Kirim WA</button>
        <button class="btn btn-danger" onclick="window.location.href='../index.php'">Home</button>
      </div>

      <div class="list-title">Daftar Menunggu</div>
      <div class="antrian-list" id="list_nonracik"></div>
      <div class="list-title">Daftar Terlewati</div>
      <div class="antrian-list" id="list_skip_nonracik"></div>
    </div>

    <div class="antrian-box">
      <h2>ANTRIAN RACIK</h2>
      <div class="antrian-number" id="racik_antrian">000</div>
      <div class="antrian-name" id="racik_nama">-</div>

      <div class="btn-grid">
        <button class="btn" onclick="panggil('Racik')">Panggil</button>
        <button class="btn btn-soft" onclick="panggilUlang('Racik')">Ulangi</button>
        <button class="btn btn-warning" onclick="lewati('Racik')">Lewati</button>
        <button class="btn btn-success" onclick="panggilTerlewati('Racik')">Panggil Terlewati</button>
        <button class="btn btn-soft full" onclick="kirimWA('Racik')">Kirim WA</button>
      </div>

      <div class="list-title">Daftar Menunggu</div>
      <div class="antrian-list" id="list_racik"></div>
      <div class="list-title">Daftar Terlewati</div>
      <div class="antrian-list" id="list_skip_racik"></div>
    </div>
  </div>

  <script>
    const lastAntrian = {
      "Non Racik": { nomor: "000", nama: "-" },
      "Racik":     { nomor: "000", nama: "-" }
    };

    async function syncLastAntrianFromServer() {
      try {
        const res = await fetch('get_last_antrian.php?_=' + Date.now(), { cache: 'no-store' });
        const data = await res.json();
        ['Non Racik', 'Racik'].forEach((jenis) => {
          if (!data[jenis]) return;
          const nomor = (data[jenis].nomor || '').toString().trim();
          if (nomor !== '' && nomor !== '000') {
            lastAntrian[jenis] = {
              nomor: nomor,
              nama: (data[jenis].nama || '-').toString()
            };
            const idPrefix = (jenis === 'Non Racik') ? 'nonracik' : 'racik';
            document.getElementById(idPrefix + '_antrian').innerText = nomor;
            document.getElementById(idPrefix + '_nama').innerText = lastAntrian[jenis].nama;
          }
        });
      } catch (_) {}
    }

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

    function loadDaftarTerlewati(jenis){
      fetch('get_skipped_list.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'jenis='+encodeURIComponent(jenis)
      })
      .then(r=>r.json())
      .then(data=>{
        const idList = (jenis==='Non Racik') ? 'list_skip_nonracik' : 'list_skip_racik';
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

    loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
    loadDaftarTerlewati('Non Racik'); loadDaftarTerlewati('Racik');
    syncLastAntrianFromServer();
    setInterval(()=>{
      loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
      loadDaftarTerlewati('Non Racik'); loadDaftarTerlewati('Racik');
      syncLastAntrianFromServer();
    }, 10000);

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

          fetch('update_audio.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'nomor='+encodeURIComponent(d.no_antrian)+'&jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)
          }).catch(()=>{});
          loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
          loadDaftarTerlewati('Non Racik'); loadDaftarTerlewati('Racik');
        }else{
          alert('Tidak ada antrian '+jenis+' tersedia.');
        }
      });
    }

    async function panggilUlang(jenis){
      await syncLastAntrianFromServer();
      const antri = lastAntrian[jenis];
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if(!loket){ alert('Pilih loket terlebih dahulu!'); return; }
      if(antri.nomor==='000'){ alert('Belum ada antrian yang dipanggil untuk '+jenis); return; }

      fetch('update_audio.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'nomor='+encodeURIComponent(antri.nomor)+'&jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)
      });
    }

    async function lewati(jenis){
      await syncLastAntrianFromServer();
      const antri = lastAntrian[jenis];
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if(!loket){ alert('Pilih loket terlebih dahulu!'); return; }
      if(antri.nomor==='000'){ alert('Belum ada antrian yang dipanggil untuk '+jenis); return; }

      fetch('skip_antrian.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)+'&no_antrian='+encodeURIComponent(antri.nomor)
      })
      .then(r=>r.json())
      .then(d=>{
        if(d.status==='sukses'){
          alert(`Antrian ${d.no_antrian} dilewati.`);
          loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
          loadDaftarTerlewati('Non Racik'); loadDaftarTerlewati('Racik');
        }else if(d.status==='kosong'){
          alert('Tidak ada antrian untuk dilewati.');
        }else{
          alert(d.pesan || 'Gagal melewati antrian.');
        }
      });
    }

    function panggilTerlewati(jenis){
      const loket = document.querySelector('input[name="loket"]:checked')?.value;
      if(!loket){ alert('Pilih loket terlebih dahulu!'); return; }

      fetch('panggil_terlewati.php', {
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

          fetch('update_audio.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'nomor='+encodeURIComponent(d.no_antrian)+'&jenis='+encodeURIComponent(jenis)+'&loket='+encodeURIComponent(loket)
          }).catch(()=>{});

          loadDaftarAntrian('Non Racik'); loadDaftarAntrian('Racik');
          loadDaftarTerlewati('Non Racik'); loadDaftarTerlewati('Racik');
        }else if(d.status==='kosong'){
          alert('Tidak ada antrian terlewati.');
        }else{
          alert(d.pesan || 'Gagal memanggil antrian terlewati.');
        }
      });
    }

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

  <div class="footer">&copy; 2026 n2N-Sistem Antrian Apotek</div>
</body>
</html>
