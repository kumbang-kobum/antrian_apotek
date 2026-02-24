<?php
$selectedDate = $_GET['tanggal'] ?? date('Y-m-d');
$validDate = DateTime::createFromFormat('Y-m-d', $selectedDate);
if (!$validDate || $validDate->format('Y-m-d') !== $selectedDate) {
    $selectedDate = date('Y-m-d');
}

$logFile = __DIR__ . '/../logs/audit.log';
$hasLogFile = file_exists($logFile);

$totals = [
    'antrian.simpan.baru' => 0,
    'antrian.simpan.existing' => 0,
    'antrian.simpan.validasi_gagal' => 0,
    'antrian.simpan.gagal' => 0,
    'antrian.panggil.sukses' => 0,
    'antrian.panggil.kosong' => 0,
    'antrian.panggil.validasi_gagal' => 0,
    'antrian.panggil.gagal' => 0,
    'antrian.ulangi.sukses' => 0,
    'antrian.ulangi.gagal' => 0,
    'antrian.ulangi.validasi_gagal' => 0
];
$perJenisPanggil = ['Racik' => 0, 'Non Racik' => 0];
$perLoket = [];
$perJam = array_fill(0, 24, 0);
$matchedRows = 0;

if ($hasLogFile) {
    $fh = fopen($logFile, 'r');
    if ($fh) {
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $row = json_decode($line, true);
            if (!is_array($row)) {
                continue;
            }

            $ts = (string)($row['ts'] ?? '');
            $event = (string)($row['event'] ?? '');
            if ($ts === '' || $event === '') {
                continue;
            }

            $date = substr($ts, 0, 10);
            if ($date !== $selectedDate) {
                continue;
            }

            $matchedRows++;
            if (isset($totals[$event])) {
                $totals[$event]++;
            }

            $hour = (int)substr($ts, 11, 2);
            if ($hour >= 0 && $hour <= 23) {
                $perJam[$hour]++;
            }

            $data = $row['data'] ?? [];
            if ($event === 'antrian.panggil.sukses') {
                $jenis = (string)($data['jenis'] ?? '');
                if (isset($perJenisPanggil[$jenis])) {
                    $perJenisPanggil[$jenis]++;
                }
                $loket = trim((string)($data['loket'] ?? ''));
                if ($loket !== '') {
                    if (!isset($perLoket[$loket])) {
                        $perLoket[$loket] = 0;
                    }
                    $perLoket[$loket]++;
                }
            }
        }
        fclose($fh);
    }
}

ksort($perLoket, SORT_NATURAL);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Harian Antrian</title>
  <style>
    :root {
      --bg: #747a84;
      --panel: #575d67;
      --line: rgba(255, 255, 255, .18);
      --text: #f4f7fb;
      --muted: #d5dce6;
      --num: #ffd35b;
      --btn-a: #31363f;
      --btn-b: #22262d;
      --shadow: 0 12px 24px rgba(0,0,0,.22);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text);
      background: var(--bg);
    }

    .wrap { max-width: 1120px; margin: 20px auto; padding: 0 14px 22px; }

    .top {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 16px;
      box-shadow: var(--shadow);
      padding: 18px;
      display: flex;
      gap: 10px;
      align-items: end;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }

    h2 { margin: 0 0 6px; letter-spacing: .2px; }
    .muted { color: var(--muted); }

    .card {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 16px;
      box-shadow: var(--shadow);
    }

    .grid { display: grid; gap: 12px; margin-bottom: 12px; }
    .grid.stats { grid-template-columns: repeat(4, minmax(170px, 1fr)); }
    .grid.main { grid-template-columns: 1.2fr 1fr; }

    .num {
      font-size: clamp(28px, 4vw, 38px);
      font-weight: 800;
      color: var(--num);
      line-height: 1;
      margin-top: 6px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      background: rgba(255,255,255,.05);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 9px 10px;
      border-bottom: 1px solid rgba(255,255,255,.11);
      text-align: left;
    }
    th {
      color: #eef4fb;
      background: rgba(0,0,0,.14);
      font-weight: 700;
    }
    tr:last-child td { border-bottom: 0; }

    .btn,
    input[type="date"] {
      border: 0;
      border-radius: 9px;
      padding: 9px 12px;
      font-size: 14px;
    }

    input[type="date"] {
      background: rgba(255,255,255,.14);
      color: var(--text);
      border: 1px solid rgba(255,255,255,.2);
    }

    .btn {
      color: #fff;
      text-decoration: none;
      background: linear-gradient(180deg, var(--btn-a), var(--btn-b));
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      cursor: pointer;
    }
    .btn:hover { filter: brightness(1.08); }

    .form-inline { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

    @media (max-width: 920px) {
      .grid.stats { grid-template-columns: repeat(2, minmax(150px, 1fr)); }
      .grid.main { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <div style="flex:1 1 260px;">
        <h2>Laporan Harian Antrian</h2>
        <div class="muted">Ringkasan dari <code>logs/audit.log</code></div>
      </div>

      <form method="get" class="form-inline">
        <input type="date" name="tanggal" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn" type="submit">Tampilkan</button>
      </form>

      <a class="btn" href="../index.php">Home</a>
    </div>

    <?php if (!$hasLogFile): ?>
      <div class="card">File log belum ada. Jalankan aktivitas antrian dulu agar laporan terisi.</div>
    <?php else: ?>
      <div class="grid stats">
        <div class="card"><div class="muted">Total Event Tercatat</div><div class="num"><?= $matchedRows ?></div></div>
        <div class="card"><div class="muted">Ambil Baru</div><div class="num"><?= $totals['antrian.simpan.baru'] ?></div></div>
        <div class="card"><div class="muted">Panggil Sukses</div><div class="num"><?= $totals['antrian.panggil.sukses'] ?></div></div>
        <div class="card"><div class="muted">Ulangi Sukses</div><div class="num"><?= $totals['antrian.ulangi.sukses'] ?></div></div>
      </div>

      <div class="grid main">
        <div class="card">
          <h3 style="margin-top:0;">Detail Status</h3>
          <table>
            <tr><th>Event</th><th>Jumlah</th></tr>
            <?php foreach ($totals as $event => $count): ?>
              <tr><td><?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?></td><td><?= $count ?></td></tr>
            <?php endforeach; ?>
          </table>
        </div>

        <div class="card">
          <h3 style="margin-top:0;">Panggil per Jenis</h3>
          <table>
            <tr><th>Jenis</th><th>Jumlah</th></tr>
            <tr><td>Non Racik</td><td><?= $perJenisPanggil['Non Racik'] ?></td></tr>
            <tr><td>Racik</td><td><?= $perJenisPanggil['Racik'] ?></td></tr>
          </table>

          <h3>Panggil per Loket</h3>
          <table>
            <tr><th>Loket</th><th>Jumlah</th></tr>
            <?php if (empty($perLoket)): ?>
              <tr><td colspan="2">Belum ada data</td></tr>
            <?php else: ?>
              <?php foreach ($perLoket as $loket => $jumlah): ?>
                <tr><td><?= htmlspecialchars($loket, ENT_QUOTES, 'UTF-8') ?></td><td><?= $jumlah ?></td></tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </table>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-top:0;">Distribusi Jam</h3>
        <table>
          <tr><th>Jam</th><th>Jumlah Event</th></tr>
          <?php foreach ($perJam as $jam => $count): ?>
            <tr><td><?= str_pad((string)$jam, 2, '0', STR_PAD_LEFT) ?>:00</td><td><?= $count ?></td></tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
