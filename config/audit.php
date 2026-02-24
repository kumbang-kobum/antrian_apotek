<?php

if (!function_exists('audit_log')) {
    /**
     * Audit log ringan dalam format JSON Lines.
     * File target: /logs/audit.log
     */
    function audit_log(string $event, array $data = []): void
    {
        try {
            $baseDir = dirname(__DIR__);
            $logDir = $baseDir . '/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }
            @chmod($logDir, 0777);

            $entry = [
                'ts' => date('c'),
                'event' => $event,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '-',
                'method' => $_SERVER['REQUEST_METHOD'] ?? '-',
                'uri' => $_SERVER['REQUEST_URI'] ?? '-',
                'ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? '-'), 0, 200),
                'data' => $data
            ];

            $logFile = $logDir . '/audit.log';
            @file_put_contents(
                $logFile,
                json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
            @chmod($logFile, 0666);
        } catch (Throwable $e) {
            // Jangan mengganggu flow utama bila logging gagal.
        }
    }
}
