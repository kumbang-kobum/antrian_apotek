<!-- 
 Pembuat Chandra Irawan M.T.I
 Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
 sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
 bagi yang ingin berdonasi secangkir kopi bisa melalui
 BCA 8110400102 A/N Chandra Irawan 
 ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
 pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
 Emirza Wira M.T.I yang berbentul exe -->
<?php
$host = "localhost";
$db   = "sik";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>