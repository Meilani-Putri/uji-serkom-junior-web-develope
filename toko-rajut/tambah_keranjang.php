<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/keranjang.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produkId = (int)($_POST['produk_id'] ?? 0);
    $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));

    $stmt = $pdo->prepare("SELECT id, stok FROM produk WHERE id = ?");
    $stmt->execute([$produkId]);
    $produk = $stmt->fetch();

    if ($produk && (int)$produk['stok'] > 0) {
        keranjang_tambah($produkId, $jumlah, (int)$produk['stok']);
    }
}

$kembali = $_POST['kembali'] ?? 'produk.php';
// Cegah open redirect: hanya izinkan path relatif di dalam folder toko-rajut
if (!preg_match('/^[a-zA-Z0-9_\-\.\/?=&]*$/', $kembali) || str_starts_with($kembali, '//')) {
    $kembali = 'produk.php';
}

header('Location: ' . $kembali);
exit;