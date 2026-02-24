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

$noRawat = trim($_POST['no_rawat'] ?? '');
$noResep = trim($_POST['no_resep'] ?? '');
$resep = trim($_POST['resep'] ?? '');
$today = date('Y-m-d');

if ($noRawat === '' || $noResep === '' || $resep === '') {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Data tidak lengkap']);
    exit;
}

if (!in_array($resep, ['Racik', 'Non Racik'], true)) {
    echo json_encode(['status' => 'gagal', 'pesan' => 'Jenis resep tidak valid']);
    exit;
}

$lockKey = 'antrian_farmasi_rajal_' . strtolower(str_replace(' ', '_', $resep)) . '_' . $today;

try {
    $stmtLock = $pdo->prepare("SELECT GET_LOCK(?, 10)");
    $stmtLock->execute([$lockKey]);
    $isLocked = (int)$stmtLock->fetchColumn() === 1;
    if (!$isLocked) {
        throw new Exception('Sistem sedang sibuk, coba lagi');
    }

    $pdo->beginTransaction();

    // Idempotent: jika resep ini sudah tersimpan hari ini, kembalikan nomor yang sama.
    $stmtCek = $pdo->prepare("SELECT no_antrian FROM antrian_farmasi_rajal WHERE tgl_antri=? AND no_resep=? LIMIT 1");
    $stmtCek->execute([$today, $noResep]);
    $existingNo = $stmtCek->fetchColumn();

    if ($existingNo !== false) {
        $pdo->commit();
        echo json_encode([
            'status' => 'sukses',
            'no_antrian' => str_pad((string)$existingNo, 3, '0', STR_PAD_LEFT),
            'existing' => true
        ]);
    } else {
        $stmtMax = $pdo->prepare("SELECT COALESCE(MAX(CAST(no_antrian AS UNSIGNED)), 0) FROM antrian_farmasi_rajal WHERE resep=? AND tgl_antri=?");
        $stmtMax->execute([$resep, $today]);
        $nextNo = (int)$stmtMax->fetchColumn() + 1;
        $noAntrian = str_pad((string)$nextNo, 3, '0', STR_PAD_LEFT);

        $stmtInsert = $pdo->prepare("INSERT INTO antrian_farmasi_rajal (no_rawat, no_resep, no_antrian, status, tgl_antri, resep)
                                     VALUES (?, ?, ?, '0', CURDATE(), ?)");
        $stmtInsert->execute([$noRawat, $noResep, $noAntrian, $resep]);

        $pdo->commit();
        echo json_encode([
            'status' => 'sukses',
            'no_antrian' => $noAntrian,
            'existing' => false
        ]);
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'gagal', 'pesan' => $e->getMessage()]);
} finally {
    try {
        $stmtUnlock = $pdo->prepare("SELECT RELEASE_LOCK(?)");
        $stmtUnlock->execute([$lockKey]);
    } catch (Throwable $unlockError) {
        // Abaikan error unlock agar response utama tetap terkirim.
    }
}
?>
