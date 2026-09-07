<?php
require_once __DIR__ . '/env.php';

function ambil_env(string $kunci, string $default): string
{
    $nilai = getenv($kunci);
    if ($nilai === false || $nilai === '') {
        return $default;
    }
    return $nilai;
}

$DB_HOST = ambil_env('DB_HOST', 'localhost');
$DB_PORT = ambil_env('DB_PORT', '5432');
$DB_NAME = ambil_env('DB_NAME', 'simpul_rajut');
$DB_USER = ambil_env('DB_USER', 'postgres');
$DB_PASS = ambil_env('DB_PASS', 'postgres');

try {
    $pdo = new PDO(
        "pgsql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME}",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan PostgreSQL aktif, database "simpul_rajut" sudah dibuat, dan ekstensi pdo_pgsql sudah aktif di php.ini. Detail: ' . htmlspecialchars($e->getMessage()));
}