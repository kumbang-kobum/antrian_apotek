<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan 
//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
//  Emirza Wira M.T.I yang berbentul exe
include '../config/database.php';
require_once __DIR__ . '/../config/skipped.php';
header('Content-Type: application/json');

function maskNamaPasien($nama) {
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
    // Jaga kompatibilitas frontend: kirim array kosong jika jenis invalid.
    echo json_encode([]);
    exit;
}

try {
    $skipped = getSkippedList($jenis, date('Y-m-d'));
    $sql = "SELECT a.no_antrian, p.nm_pasien AS nama
    FROM antrian_farmasi_rajal a
    JOIN resep_obat r ON a.no_resep = r.no_resep
    JOIN reg_periksa rp ON r.no_rawat = rp.no_rawat
    JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
    WHERE a.status = '0' AND a.resep = ? AND a.tgl_antri = CURDATE()
    ";
    $params = [$jenis];
    if (!empty($skipped)) {
        $in = implode(',', array_fill(0, count($skipped), '?'));
        $sql .= " AND a.no_resep NOT IN ($in)";
        $params = array_merge($params, $skipped);
    }
    $sql .= " ORDER BY a.no_antrian ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$row) {
        $row['nama'] = maskNamaPasien($row['nama']);
    }
    unset($row);

    echo json_encode($data);
} catch (Throwable $e) {
    // Jaga kompatibilitas frontend: saat gagal tetap kirim array kosong.
    echo json_encode([]);
}
?>
