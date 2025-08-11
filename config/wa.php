<?php
// config/wa.php
return [
  // ganti sesuai WAHA-mu:
  'endpoint' => 'https://xxx.xxxx/api/sendText', // contoh endpoint
  'mode'     => 'receiver', // 'receiver' untuk payload {receiver, message}; 
                            // kalau instance-mu pakai {chatId, text}, ganti ke 'chatId'
];