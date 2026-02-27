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
header('Content-Type: application/json');

$no_rawat = trim($_POST['no_rawat'] ?? '');
$today = (string) $pdo->query("SELECT CURDATE()")->fetchColumn();
if ($today === '') {
    $today = date('Y-m-d');
}

if ($no_rawat === '') {
    echo json_encode(['status' => 'gagal', 'pesan' => 'No. rawat wajib diisi']);
    exit;
}

try {
    // Gunakan tanggal resep terbaru untuk no_rawat ini agar resep lama tidak ikut terbaca ulang.
    $stmtLatest = $pdo->prepare("
        SELECT MAX(tgl_peresepan)
        FROM resep_obat
        WHERE no_rawat = ? AND status='ralan' AND tgl_peresepan <= ?
    ");
    $stmtLatest->execute([$no_rawat, $today]);
    $latestDate = $stmtLatest->fetchColumn();

    if (!$latestDate) {
        echo json_encode(['status' => 'tidak ditemukan', 'pesan' => 'Data resep tidak ditemukan / sudah diambil']);
        exit;
    }

    // Cek racik
    $sqlRacik = "SELECT b.no_rawat, b.no_resep, c.nm_pasien, c.alamat, c.no_tlp
    FROM reg_periksa a
    JOIN resep_obat b ON b.no_rawat = a.no_rawat
    JOIN pasien c ON a.no_rkm_medis = c.no_rkm_medis
    WHERE EXISTS (SELECT 1 FROM resep_dokter_racikan rr WHERE rr.no_resep = b.no_resep)
    AND b.status='ralan' AND b.tgl_peresepan = ? AND b.no_rawat=?
    AND NOT EXISTS (SELECT 1 FROM antrian_farmasi_rajal d WHERE d.tgl_antri=? AND d.no_resep = b.no_resep)
    ORDER BY b.tgl_peresepan DESC, b.jam_peresepan DESC, b.no_resep DESC
    LIMIT 1";

    $stmt = $pdo->prepare($sqlRacik);
    $stmt->execute([$latestDate, $no_rawat, $today]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $jenis = 'Racik';

    if (!$data) {
        // Non racik
        $sqlNon = "SELECT b.no_rawat, b.no_resep, c.nm_pasien, c.alamat, c.no_tlp
        FROM reg_periksa a
        JOIN resep_obat b ON b.no_rawat = a.no_rawat
        JOIN pasien c ON a.no_rkm_medis = c.no_rkm_medis
        WHERE NOT EXISTS (SELECT 1 FROM resep_dokter_racikan rr WHERE rr.no_resep = b.no_resep)
        AND b.status='ralan' AND b.tgl_peresepan = ? AND b.no_rawat=?
        AND NOT EXISTS (SELECT 1 FROM antrian_farmasi_rajal d WHERE d.tgl_antri=? AND d.no_resep = b.no_resep)
        ORDER BY b.tgl_peresepan DESC, b.jam_peresepan DESC, b.no_resep DESC
        LIMIT 1";

        $stmt = $pdo->prepare($sqlNon);
        $stmt->execute([$latestDate, $no_rawat, $today]);
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
        // Fallback info: cek apakah sebenarnya sudah pernah diambil hari ini.
        $stmtExisting = $pdo->prepare("
            SELECT a.no_antrian, a.resep
            FROM antrian_farmasi_rajal a
            WHERE a.tgl_antri = ? AND a.no_rawat = ?
            ORDER BY a.id DESC
            LIMIT 1
        ");
        $stmtExisting->execute([$today, $no_rawat]);
        $existing = $stmtExisting->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            echo json_encode([
                'status' => 'tidak ditemukan',
                'pesan' => 'Resep untuk no. rawat ini sudah pernah diambil hari ini pada nomor ' . $existing['no_antrian'] . ' (' . $existing['resep'] . ')'
            ]);
        } else {
            echo json_encode(['status' => 'tidak ditemukan', 'pesan' => 'Data resep tidak ditemukan / sudah diambil']);
        }
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
}
?>
