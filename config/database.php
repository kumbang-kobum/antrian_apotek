<?php
// Pembuat Chandra Irawan M.T.I
//  Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
//  sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
//  bagi yang ingin berdonasi secangkir kopi bisa melalui
//  BCA 8110400102 A/N Chandra Irawan 
//  ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
//  pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
//  Emirza Wira M.T.I yang berbentul exe
$host = "localhost";
$db   = "sik";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Auto-cleanup khusus tabel antrian farmasi (retensi 14 hari), dijalankan maksimal 1x/hari.
if (!function_exists('cleanupAntrianFarmasiRajal')) {
    function cleanupAntrianFarmasiRajal(PDO $pdo, int $retentionDays = 14): void
    {
        try {
            $baseDir = dirname(__DIR__);
            $logDir = $baseDir . '/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }

            $markerFile = $logDir . '/cleanup_antrian_farmasi_rajal.marker';
            $today = date('Y-m-d');
            $lastRun = @file_get_contents($markerFile);

            if (trim((string)$lastRun) === $today) {
                return;
            }

            $stmt = $pdo->prepare(
                "DELETE FROM antrian_farmasi_rajal
                 WHERE tgl_antri < CURDATE() - INTERVAL :days DAY"
            );
            $stmt->bindValue(':days', $retentionDays, PDO::PARAM_INT);
            $stmt->execute();

            @file_put_contents($markerFile, $today, LOCK_EX);
        } catch (Throwable $e) {
            // Jangan hentikan aplikasi jika cleanup gagal.
        }
    }
}

cleanupAntrianFarmasiRajal($pdo, 14);
?>
