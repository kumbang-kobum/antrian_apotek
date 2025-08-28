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

$no_rawat = $_POST['no_rawat'] ?? '';
$today = date('Y-m-d');

// Cek racik
$sqlRacik = "SELECT b.no_rawat, b.no_resep, c.nm_pasien, c.alamat, c.no_tlp
FROM reg_periksa a
JOIN resep_obat b ON b.no_rawat = a.no_rawat
JOIN pasien c ON a.no_rkm_medis = c.no_rkm_medis
WHERE b.no_resep IN (SELECT no_resep FROM resep_dokter_racikan)
AND b.status='ralan' AND b.tgl_peresepan=? AND b.no_rawat=?
AND NOT EXISTS (SELECT 1 FROM antrian_farmasi_rajal d WHERE d.tgl_antri=? AND d.no_resep = b.no_resep)
LIMIT 1";

$stmt = $pdo->prepare($sqlRacik);
$stmt->execute([$today, $no_rawat, $today]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$jenis = 'Racik';

if (!$data) {
    // Non racik
    $sqlNon = "SELECT b.no_rawat, b.no_resep, c.nm_pasien, c.alamat, c.no_tlp
    FROM reg_periksa a
    JOIN resep_obat b ON b.no_rawat = a.no_rawat
    JOIN pasien c ON a.no_rkm_medis = c.no_rkm_medis
    WHERE b.no_resep NOT IN (SELECT no_resep FROM resep_dokter_racikan)
    AND b.status='ralan' AND b.tgl_peresepan=? AND b.no_rawat=?
    AND NOT EXISTS (SELECT 1 FROM antrian_farmasi_rajal d WHERE d.tgl_antri=? AND d.no_resep = b.no_resep)
    LIMIT 1";

    $stmt = $pdo->prepare($sqlNon);
    $stmt->execute([$today, $no_rawat, $today]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $jenis = 'Non Racik';
}

if ($data) {
    $stmt = $pdo->prepare("SELECT MAX(CAST(no_antrian AS UNSIGNED)) FROM antrian_farmasi_rajal WHERE resep=? AND tgl_antri=?");
    $stmt->execute([$jenis, $today]);
    $max = $stmt->fetchColumn();
    $next = str_pad((int)$max + 1, 3, '0', STR_PAD_LEFT);

    echo json_encode([
        'status' => 'sukses',
        'no_resep' => $data['no_resep'],
        'no_rawat' => $data['no_rawat'],
        'nm_pasien' => $data['nm_pasien'],
        'resep' => $jenis,
        'no_antrian' => $next,
        'alamat' => $data['alamat'] ?? '',
        'no_tlp' => $data['no_tlp'] ?? ''
    ]);
} else {
    echo json_encode(['status' => 'tidak ditemukan']);
}
?>