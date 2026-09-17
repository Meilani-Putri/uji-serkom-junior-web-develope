<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $cek = $pdo->prepare("SELECT COUNT(*) AS total FROM produk WHERE kategori_id = ?");
    $cek->execute([$id]);
    $jumlahProduk = (int)$cek->fetch()['total'];

    if ($jumlahProduk > 0) {
        header('Location: kategori.php?error=masih_dipakai');
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: kategori.php?status=terhapus');
exit;