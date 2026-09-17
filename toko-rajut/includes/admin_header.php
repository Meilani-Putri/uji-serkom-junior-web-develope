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
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css?v=2">
</head>
<body class="admin-body">
<div class="admin-shell">
  <aside class="admin-side">
    <div class="admin-side__brand">
      <span class="admin-side__mark">SR</span>
      <span class="admin-side__name">Amoura Atelier<br><small>Admin Panel</small></span>
    </div>
    <nav class="admin-side__nav">
      <a href="index.php" class="<?= $halaman_admin === 'produk' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 7l2.5-4h13L21 7"/><path d="M9 11v3M15 11v3"/></svg>
        <span>Daftar produk</span>
      </a>
      <a href="tambah.php" class="<?= $halaman_admin === 'tambah' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
        <span>Tambah produk</span>
      </a>
      <a href="kategori.php" class="<?= $halaman_admin === 'kategori' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 11.6 12.4 3.4a2 2 0 0 0-1.4-.6H5a2 2 0 0 0-2 2v6a2 2 0 0 0 .6 1.4l8.2 8.2a2 2 0 0 0 2.8 0l6-6a2 2 0 0 0 0-2.8Z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor" stroke="none"/></svg>
        <span>Kategori</span>
      </a>
      <a href="laporan.php" class="<?= $halaman_admin === 'laporan' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M12 20V4M20 20v-7"/></svg>
        <span>Laporan penjualan</span>
      </a>
    </nav>
    <div class="admin-side__foot">
      <a href="/toko-rajut/index.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        Lihat situs publik
      </a>
      <a href="logout.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        Keluar (<?= bersihkan($_SESSION['admin_username'] ?? '') ?>)
      </a>
    </div>
  </aside>
  <main class="admin-main">
    <div class="admin-topbar">
      <p class="admin-topbar__greeting">Selamat datang, <strong><?= bersihkan($_SESSION['admin_username'] ?? 'admin') ?></strong></p>
      <span class="admin-topbar__date"><?= bersihkan(date('d F Y')) ?></span>
    </div>
</parameter>