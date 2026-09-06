<?php
require_once __DIR__ . '/env.php';
// Sesuaikan bagian ini dengan setting PostgreSQL kamu.
$DB_HOST = 'localhost';
$DB_PORT = '5432';
$DB_NAME = 'simpul_rajut';
$DB_USER = 'postgres';
$DB_PASS = 'postgres'; // password yang kamu buat saat install PostgreSQL

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