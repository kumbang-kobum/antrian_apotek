<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/skipped.php';
header('Content-Type: application/json');

function maskNamaPasienSkipped($nama) {
    $parts = explode(' ', $nama);
    $masked = [];
    foreach ($parts as $part) {
        if (strlen($part) <= 2) {
            $masked[] = $part;
        } else {
            $masked[] = substr($part, 0, 2) . str_repeat('x', strlen($part) - 2);
        }
    }
    return implode(' ', $masked);
}

$jenis = trim($_POST['jenis'] ?? 'Non Racik');
if (!in_array($jenis, ['Non Racik', 'Racik'], true)) {
    echo json_encode([]);
    exit;
}

try {
    $today = date('Y-m-d');
    $skipped = getSkippedList($jenis, $today);
    if (empty($skipped)) {
        echo json_encode([]);
        exit;
    }

    $in = implode(',', array_fill(0, count($skipped), '?'));
    $sql = "
      SELECT a.no_antrian, p.nm_pasien AS nama, a.no_resep
      FROM antrian_farmasi_rajal a
      JOIN resep_obat r ON a.no_resep = r.no_resep
      JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
      JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
      WHERE a.resep = ? AND a.tgl_antri = CURDATE() AND a.status='0' AND a.no_resep IN ($in)
    ";
    $params = array_merge([$jenis], $skipped);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orderMap = [];
    foreach ($skipped as $i => $noResep) {
        $orderMap[$noResep] = $i;
    }

    usort($rows, function ($a, $b) use ($orderMap) {
        $ia = $orderMap[$a['no_resep']] ?? 999999;
        $ib = $orderMap[$b['no_resep']] ?? 999999;
        return $ia <=> $ib;
    });

    $existing = [];
    foreach ($rows as $row) {
        $existing[$row['no_resep']] = true;
    }
    $cleaned = array_values(array_filter($skipped, fn($nr) => isset($existing[$nr])));
    if ($cleaned !== $skipped) {
        setSkippedList($jenis, $today, $cleaned);
    }

    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'no_antrian' => $row['no_antrian'],
            'nama' => maskNamaPasienSkipped($row['nama']),
        ];
    }
    echo json_encode($out);
} catch (Throwable $e) {
    echo json_encode([]);
}

