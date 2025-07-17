<?php
header('Content-Type: application/json');
$path = 'last_antrian.json';
if (file_exists($path)) {
    echo file_get_contents($path);
} else {
    echo json_encode([
        "Non Racik" => ["nomor" => "000", "nama" => "-"],
        "Racik" => ["nomor" => "000", "nama" => "-"]
    ]);
}