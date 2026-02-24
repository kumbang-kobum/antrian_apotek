<?php

if (!function_exists('skippedFilePath')) {
    function skippedFilePath(): string
    {
        return dirname(__DIR__) . '/panggil/skipped_antrian.json';
    }
}

if (!function_exists('loadSkippedData')) {
    function loadSkippedData(): array
    {
        $file = skippedFilePath();
        if (!file_exists($file)) {
            return [];
        }

        $raw = @file_get_contents($file);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}

if (!function_exists('saveSkippedData')) {
    function saveSkippedData(array $data): void
    {
        $file = skippedFilePath();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        @chmod($file, 0666);
    }
}

if (!function_exists('getSkippedList')) {
    function getSkippedList(string $jenis, string $tanggal): array
    {
        $data = loadSkippedData();
        $list = $data[$tanggal][$jenis] ?? [];
        return is_array($list) ? array_values(array_unique($list)) : [];
    }
}

if (!function_exists('setSkippedList')) {
    function setSkippedList(string $jenis, string $tanggal, array $list): void
    {
        $data = loadSkippedData();
        if (!isset($data[$tanggal]) || !is_array($data[$tanggal])) {
            $data[$tanggal] = [];
        }
        $clean = array_values(array_unique(array_filter($list, fn($v) => is_string($v) && $v !== '')));
        $data[$tanggal][$jenis] = $clean;
        saveSkippedData($data);
    }
}

if (!function_exists('addSkippedNoResep')) {
    function addSkippedNoResep(string $jenis, string $tanggal, string $noResep): void
    {
        $list = getSkippedList($jenis, $tanggal);
        if (!in_array($noResep, $list, true)) {
            $list[] = $noResep;
            setSkippedList($jenis, $tanggal, $list);
        }
    }
}

if (!function_exists('removeSkippedNoResep')) {
    function removeSkippedNoResep(string $jenis, string $tanggal, string $noResep): void
    {
        $list = getSkippedList($jenis, $tanggal);
        $list = array_values(array_filter($list, fn($v) => $v !== $noResep));
        setSkippedList($jenis, $tanggal, $list);
    }
}

if (!function_exists('popSkippedNoResep')) {
    function popSkippedNoResep(string $jenis, string $tanggal): ?string
    {
        $list = getSkippedList($jenis, $tanggal);
        if (empty($list)) {
            return null;
        }
        $first = array_shift($list);
        setSkippedList($jenis, $tanggal, $list);
        return $first;
    }
}

