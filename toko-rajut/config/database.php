<?php
require_once __DIR__ . '/env.php';

// Kalau berjalan di Railway, variabel ini sudah otomatis tersedia (dari tab Variables).
// Kalau berjalan di laptop (lokal), akan pakai nilai default localhost di bawah ini.
$DB_HOST = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$DB_PORT = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432';
$DB_NAME = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'simpul_rajut';
$DB_USER = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'postgres';
$DB_PASS = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'postgres';

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