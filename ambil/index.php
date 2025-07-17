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

        input[type="text"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h3>AMBIL ANTRIAN</h3>
        <input type="text" id="no_rawat" placeholder="Masukkan No. Rawat...">
        <button onclick="ambil()">Ambil Antrian</button>

        <div id="hasil"></div>
    </div>

    <script>
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
                document.getElementById('hasil').innerHTML = `
                    <hr>
                    <strong>Nama:</strong> ${data.nm_pasien}<br>
                    <strong>No. Resep:</strong> ${data.no_resep}<br>
                    <strong>Jenis Resep:</strong> ${data.resep}<br>
                    <strong>Nomor Antrian:</strong> <b>${data.no_antrian}</b><br><br>
                    <button onclick="simpan('${data.no_rawat}', '${data.no_resep}', '${data.no_antrian}', '${data.resep}')">Simpan</button>
                `;
            } else {
                alert("Data tidak ditemukan atau sudah diambil.");
            }
        });
    }

    function simpan(no_rawat, no_resep, no_antrian, resep) {
        fetch('simpan_antrian.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `no_rawat=${no_rawat}&no_resep=${no_resep}&no_antrian=${no_antrian}&resep=${resep}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'sukses') {
                alert("Antrian berhasil disimpan!");
                location.reload();
            } else {
                alert("Gagal menyimpan antrian.");
            }
        });
    }
    </script>
</body>
</html>