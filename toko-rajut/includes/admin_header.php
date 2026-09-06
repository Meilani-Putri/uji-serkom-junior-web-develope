<?php
$halaman_admin = $halaman_admin ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? bersihkan($page_title) . ' — Admin Amoura Atelier' : 'Admin Amoura Atelier' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-side">
    <div class="admin-side__brand">
      <span class="admin-side__mark">SR</span>
      <span class="admin-side__name">Amoura Atelier<br><small>Admin Panel</small></span>
    </div>
    <nav class="admin-side__nav">
      <a href="index.php" class="<?= $halaman_admin === 'produk' ? 'active' : '' ?>">Daftar produk</a>
      <a href="tambah.php" class="<?= $halaman_admin === 'tambah' ? 'active' : '' ?>">Tambah produk</a>
    </nav>
    <div class="admin-side__foot">
      <a href="/toko-rajut/index.php">Lihat situs publik</a>
      <a href="logout.php">Keluar (<?= bersihkan($_SESSION['admin_username'] ?? '') ?>)</a>
    </div>
  </aside>
  <main class="admin-main">
</parameter>