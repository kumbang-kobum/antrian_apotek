<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$file = __DIR__ . '/last_audio.json';
if (file_exists($file)) {
    readfile($file);
} else {
    echo json_encode([]);
}

