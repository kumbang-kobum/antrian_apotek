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
    <title>Ambil Antrian</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(rgba(0,0,50,0.6), rgba(0,0,50,0.6)), url('../assets/img/bg-farmasi.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

		@media print {
		  html, body {
		    margin: 0 !important;
		    padding: 0 !important;
		    background: white !important;
		    color: black !important;
		  }

		  button, input, textarea, select, #hasil, .container > h3 {
		    display: none !important;
		  }

		  #popupCetak {
		    all: unset;
		    display: block !important;
		    width: 210px !important;
		    margin: 0 auto !important;
		    text-align: center !important;
		  }

		  #popupCetak > div {
		    all: unset;
		    display: block;
		    margin: 0;
		    padding: 0;
		  }

		  #popup_no_antrian {
		    font-size: 28pt !important;
		    margin: 0;
		    padding: 0;
		  }

		  #popup_nama_pasien,
		  #popup_jenis {
		    font-size: 12pt !important;
		    margin: 0;
		    padding: 0;
		  }

		  @page {
		    size: 75mm auto;
		    margin: 0;
		  }
		}

        .container {
            width: 350px;
            background-color: rgba(0, 0, 70, 0.85);
            padding: 30px;
            margin: 100px auto;
            border-radius: 15px;
            box-shadow: 0 0 15px #000;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"], select, textarea {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2196f3;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0b7dda;
        }

        #hasil {
            margin-top: 20px;
            background-color: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 10px;
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
    <button class="home-button" onclick="window.location.href='../index.php'">🏠 Home</button>
    <div class="container">
        <h3>AMBIL ANTRIAN</h3>
        <input type="text" id="no_rawat" placeholder="Masukkan No. Rawat...">

        <label>Jenis Ambil:</label><br>
        <select id="jenis_ambil" onchange="toggleAntarForm()">
            <option value="langsung">Ambil Hari Ini</option>
            <option value="besok">Ambil Besok</option>
            <option value="antar">Antar ke Rumah</option>
        </select><br><br>

        <div id="formAntar" style="display:none;">
            <textarea id="alamat" placeholder="Alamat Lengkap..."></textarea>
            <input type="text" id="no_tlp" placeholder="Nomor Telepon...">
        </div>

        <button onclick="ambil()">Ambil Antrian</button>
        <div id="hasil"></div>
    </div>

    <!-- Modal cetak -->
    <div id="popupCetak" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; color:black; padding:30px; border-radius:15px; width:300px; text-align:center;">
            <h3>Nomor Antrian</h3>
            <h1 id="popup_no_antrian" style="font-size:60px; margin:10px 0;">000</h1>
            <p id="popup_nama_pasien">Nama Pasien</p>
            <p id="popup_jenis">Jenis: -</p>
            <br>
            <button onclick="window.print()">Cetak</button>
            <button onclick="tutupPopup()">Tutup</button>
        </div>
    </div>

    <script>
    function toggleAntarForm() {
        const jenis = document.getElementById('jenis_ambil').value;
        const antarForm = document.getElementById('formAntar');
        antarForm.style.display = (jenis === 'antar') ? 'block' : 'none';
    }

    function ambil() {
    let no_rawat = document.getElementById('no_rawat').value;
    fetch('ambil_data.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'no_rawat=' + encodeURIComponent(no_rawat)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'sukses') {
            // Isi data di bawah hasil
            document.getElementById('hasil').innerHTML = `
                <hr>
                <strong>Nama:</strong> ${data.nm_pasien}<br>
                <strong>No. Resep:</strong> ${data.no_resep}<br>
                <strong>Jenis Resep:</strong> ${data.resep}<br>
                <strong>Nomor Antrian:</strong> <b>${data.no_antrian}</b><br><br>
                <button onclick="simpan('${data.no_rawat}', '${data.no_resep}', '${data.no_antrian}', '${data.resep}')">Simpan</button>
            `;

            // Jika jenis ambil = antar, isi otomatis alamat & telp
            const jenis = document.getElementById('jenis_ambil').value;
            if (jenis === 'antar') {
                document.getElementById('alamat').value = data.alamat || '';
                document.getElementById('no_tlp').value = data.no_tlp || '';
            }

        } else {
            alert("Data tidak ditemukan atau sudah diambil.");
        }
    });
}

    function simpan(no_rawat, no_resep, no_antrian, resep) {
        const jenis_ambil = document.getElementById('jenis_ambil').value;
        const alamat = document.getElementById('alamat')?.value || '';
        const no_tlp = document.getElementById('no_tlp')?.value || '';

        fetch('simpan_antrian.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `no_rawat=${no_rawat}&no_resep=${no_resep}&no_antrian=${no_antrian}&resep=${resep}&jenis_ambil=${jenis_ambil}&alamat=${encodeURIComponent(alamat)}&no_tlp=${encodeURIComponent(no_tlp)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'sukses') {
                alert("Antrian berhasil disimpan!");

                let nama = document.querySelector("#hasil").innerHTML.match(/<strong>Nama:<\/strong>\s(.+?)<br>/)[1];

                document.getElementById("popup_no_antrian").innerText = no_antrian;
                document.getElementById("popup_nama_pasien").innerText = nama;
                document.getElementById("popup_jenis").innerText = "Jenis: " + resep + " (" + jenis_ambil + ")";
                document.getElementById("popupCetak").style.display = "flex";

                document.getElementById("no_rawat").value = "";
                document.getElementById("hasil").innerHTML = "";
            } else {
                alert("Gagal menyimpan antrian.");
            }
        });
    }

    function tutupPopup() {
        document.getElementById("popupCetak").style.display = "none";
    }
    </script>
</body>
</html>