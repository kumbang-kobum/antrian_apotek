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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Antrian</title>
    <style>
        :root {
            --bg: #747a84;
            --text: #eef5ff;
            --muted: #b3c8df;
            --card: #575d67;
            --line: rgba(165, 211, 255, 0.24);
            --primary: #1fa6ff;
            --primary-2: #0d7fcb;
            --accent: #17b978;
            --accent-2: #0f9d66;
            --shadow: 0 20px 35px rgba(0, 0, 0, 0.38);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background: var(--bg);
            padding: 24px;
        }

        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
            }

            .home-btn,
            #no_rawat,
            #jenis_ambil,
            #formAntar,
            #hasil,
            .action-btn,
            .quick-btn,
            .popup-actions,
            .popup-actions button {
                display: none !important;
            }

            #popupCetak {
                all: unset;
                display: block !important;
                width: 210px !important;
                margin: 0 auto !important;
                text-align: center !important;
            }

            .popup-card {
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
            #popup_jenis,
            #popup_alamat {
                font-size: 12pt !important;
                margin: 0;
                padding: 0;
            }

            @page {
                size: 75mm auto;
                margin: 0;
            }
        }

        .page {
            width: min(520px, 100%);
            margin: 0 auto;
        }

        .home-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--line);
            color: #fff;
            background: rgba(2, 12, 28, 0.72);
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            margin-bottom: 14px;
            font-weight: 600;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(8px);
        }

        h3 {
            margin: 0 0 18px;
            font-size: 23px;
            text-align: center;
            letter-spacing: .5px;
        }

        label {
            display: block;
            margin: 8px 0;
            color: var(--muted);
            font-size: 14px;
        }

        input[type="text"], select, textarea {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-radius: 10px;
            padding: 12px 13px;
            font-size: 16px;
            margin-bottom: 12px;
            outline: none;
        }

        input::placeholder,
        textarea::placeholder { color: rgba(233, 244, 255, 0.62); }

        select option {
            color: #111;
            background: #fff;
        }

        .action-btn,
        .quick-btn {
            width: 100%;
            border: 0;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            padding: 12px;
            cursor: pointer;
        }

        .action-btn {
            background: linear-gradient(180deg, var(--primary), var(--primary-2));
            margin-top: 6px;
        }

        .quick-btn {
            background: linear-gradient(180deg, var(--accent), var(--accent-2));
            margin-top: 10px;
        }

        .result {
            margin-top: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 10px;
            min-height: 24px;
        }

        #popupCetak {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.62);
            justify-content: center;
            align-items: center;
            padding: 16px;
        }

        .popup-card {
            background: #fff;
            color: #111;
            border-radius: 14px;
            width: min(320px, 100%);
            text-align: center;
            padding: 24px 20px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.3);
        }

        .popup-card h1 {
            margin: 8px 0;
            font-size: 58px;
            line-height: 1;
        }

        #popup_alamat {
            margin-top: 6px;
            padding: 8px;
            border-radius: 8px;
            background: #f3f6fa;
            color: #273241;
            font-size: 14px;
            text-align: left;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .popup-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 12px;
        }

        .popup-actions button {
            border: 0;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }

        .btn-print { background: #126dc1; }
        .btn-close { background: #5a6472; }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .card { padding: 16px; border-radius: 15px; }
            h3 { font-size: 19px; }
        }
    </style>
</head>
<body>
    <div class="page">
        <button class="home-btn" onclick="window.location.href='../index.php'">🏠 Home</button>

        <div class="card">
            <h3>AMBIL ANTRIAN</h3>

            <input type="text" id="no_rawat" placeholder="Masukkan No. Rawat...">

            <label for="jenis_ambil">Jenis Ambil</label>
            <select id="jenis_ambil" onchange="toggleAntarForm()">
                <option value="langsung">Ambil Hari Ini</option>
                <option value="besok">Ambil Besok</option>
                <option value="antar">Antar ke Rumah</option>
            </select>

            <div id="formAntar" style="display:none;">
                <textarea id="alamat" placeholder="Alamat Lengkap..."></textarea>
                <input type="text" id="no_tlp" placeholder="Nomor Telepon...">
            </div>

            <button class="action-btn" onclick="ambil()">Ambil Antrian</button>
            <button class="quick-btn" onclick="ambilSimpanCetak()">Ambil + Simpan + Cetak</button>

            <div id="hasil" class="result"></div>
        </div>
    </div>

    <div id="popupCetak">
        <div class="popup-card">
            <h3>Nomor Antrian</h3>
            <h1 id="popup_no_antrian">000</h1>
            <p id="popup_nama_pasien">Nama Pasien</p>
            <p id="popup_jenis">Jenis: -</p>
            <p id="popup_alamat">Alamat: -</p>
            <div class="popup-actions">
                <button class="btn-print" onclick="window.print()">Cetak</button>
                <button class="btn-close" onclick="tutupPopup()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
    function toggleAntarForm() {
        const jenis = document.getElementById('jenis_ambil').value;
        const antarForm = document.getElementById('formAntar');
        antarForm.style.display = (jenis === 'antar') ? 'block' : 'none';
    }

    function ambil() {
        let no_rawat = document.getElementById('no_rawat').value.trim();
        if (!no_rawat) {
            alert("No. rawat wajib diisi.");
            return;
        }
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
                    <button class="action-btn" onclick="simpan('${data.no_rawat}', '${data.no_resep}', '${data.no_antrian}', '${data.resep}', '${data.nm_pasien}')">Simpan</button>
                `;

                const jenis = document.getElementById('jenis_ambil').value;
                if (jenis === 'antar') {
                    document.getElementById('alamat').value = data.alamat || '';
                    document.getElementById('no_tlp').value = data.no_tlp || '';
                }
            } else {
                alert(data.pesan || "Data tidak ditemukan atau sudah diambil.");
            }
        });
    }

    async function ambilSimpanCetak() {
        const no_rawat = document.getElementById('no_rawat').value.trim();
        if (!no_rawat) {
            alert("No. rawat wajib diisi.");
            return;
        }

        try {
            const res = await fetch('ambil_data.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'no_rawat=' + encodeURIComponent(no_rawat)
            });
            const data = await res.json();

            if (data.status !== 'sukses') {
                alert(data.pesan || "Data tidak ditemukan atau sudah diambil.");
                return;
            }

            await simpan(data.no_rawat, data.no_resep, data.no_antrian, data.resep, data.nm_pasien);
        } catch (err) {
            alert("Gagal memproses ambil+simpan cepat.");
        }
    }

    function renderAlamatPopup(jenisAmbil, alamatInput) {
        const popupAlamat = document.getElementById("popup_alamat");
        if (!popupAlamat) return;

        const alamatCetak = (alamatInput || '').trim();
        if (jenisAmbil === 'antar') {
            popupAlamat.style.display = "block";
            popupAlamat.textContent = "Alamat: " + (alamatCetak !== '' ? alamatCetak : '-');
        } else {
            popupAlamat.style.display = "none";
            popupAlamat.textContent = "Alamat: -";
        }
    }

    async function simpan(no_rawat, no_resep, no_antrian, resep, nama_pasien = '') {
        const jenis_ambil = document.getElementById('jenis_ambil').value;
        const alamat = document.getElementById('alamat')?.value || '';
        const no_tlp = document.getElementById('no_tlp')?.value || '';

        const payload = new URLSearchParams({
            no_rawat: no_rawat,
            no_resep: no_resep,
            no_antrian: no_antrian,
            resep: resep,
            jenis_ambil: jenis_ambil,
            alamat: alamat,
            no_tlp: no_tlp
        });

        try {
            const res = await fetch('simpan_antrian.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: payload.toString()
            });
            const data = await res.json();
            if (data.status === 'sukses') {
                alert("Antrian berhasil disimpan!");
                const nomorFinal = data.no_antrian || no_antrian;
                const hasilHtml = document.querySelector("#hasil").innerHTML;
                const matchNama = hasilHtml.match(/<strong>Nama:<\/strong>\s(.+?)<br>/);
                let nama = nama_pasien || (matchNama ? matchNama[1] : '-');

                document.getElementById("popup_no_antrian").innerText = nomorFinal;
                document.getElementById("popup_nama_pasien").innerText = nama;
                document.getElementById("popup_jenis").innerText = "Jenis: " + resep + " (" + jenis_ambil + ")";
                renderAlamatPopup(jenis_ambil, alamat);
                document.getElementById("popupCetak").style.display = "flex";

                document.getElementById("no_rawat").value = "";
                document.getElementById("hasil").innerHTML = "";
            } else {
                alert(data.pesan || "Gagal menyimpan antrian.");
            }
        } catch (err) {
            alert("Gagal menyimpan antrian.");
        }
    }

    function tutupPopup() {
        document.getElementById("popupCetak").style.display = "none";
    }
    </script>
</body>
</html>
