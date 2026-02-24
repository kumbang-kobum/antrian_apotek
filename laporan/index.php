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
    body{margin:0;font-family:"Segoe UI",sans-serif;background:linear-gradient(to bottom right,#004466,#001f33);color:#fff}
    .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
    .top{display:flex;gap:12px;align-items:end;flex-wrap:wrap;margin-bottom:16px}
    .card{background:rgba(0,0,70,.85);border-radius:12px;padding:16px;box-shadow:0 0 10px rgba(0,0,0,.3)}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:12px}
    .num{font-size:34px;font-weight:700;color:#ffeb3b}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border-bottom:1px solid rgba(255,255,255,.15);text-align:left}
    .btn{padding:10px 14px;border:none;border-radius:8px;background:#2196f3;color:#fff;cursor:pointer;text-decoration:none;display:inline-block}
    .btn:hover{background:#0b7dda}
    input[type="date"]{padding:9px 10px;border-radius:8px;border:none}
    .muted{color:#c7d7e2}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <div>
        <h2 style="margin:0 0 8px;">Laporan Harian Antrian</h2>
        <div class="muted">Ringkasan dari <code>logs/audit.log</code></div>
      </div>
      <form method="get" style="display:flex;gap:8px;align-items:center;">
        <input type="date" name="tanggal" value="<?= htmlspecialchars($selectedDate, ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn" type="submit">Tampilkan</button>
      </form>
      <a class="btn" href="../index.php">Home</a>
    </div>

    <?php if (!$hasLogFile): ?>
      <div class="card">File log belum ada. Jalankan aktivitas antrian dulu agar laporan terisi.</div>
    <?php else: ?>
      <div class="grid">
        <div class="card"><div class="muted">Total Event Tercatat</div><div class="num"><?= $matchedRows ?></div></div>
        <div class="card"><div class="muted">Ambil Baru</div><div class="num"><?= $totals['antrian.simpan.baru'] ?></div></div>
        <div class="card"><div class="muted">Panggil Sukses</div><div class="num"><?= $totals['antrian.panggil.sukses'] ?></div></div>
        <div class="card"><div class="muted">Ulangi Sukses</div><div class="num"><?= $totals['antrian.ulangi.sukses'] ?></div></div>
      </div>

      <div class="grid">
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

