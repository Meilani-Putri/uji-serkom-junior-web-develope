<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$kategoriNav = $pdo->query("SELECT id, nama, slug FROM kategori ORDER BY nama")->fetchAll();
$halaman_aktif = $halaman_aktif ?? '';
$jumlahKeranjang = array_sum($_SESSION['keranjang'] ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? bersihkan($page_title) . ' — Amoura Atelier' : 'Amoura Atelier — Rajut Handmade' ?></title>
<meta name="description" content="Amoura Atelier — toko rajut handmade dari Ponorogo. Sweater, cardigan, tas, dan boneka rajut yang dibuat satu per satu dengan tangan.">
<link rel="icon" href="/toko-rajut/assets/img/logo bayuu.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
</head>
<body>

<div class="topbar">
  <div class="topbar__inner">
    <span><?= ikon('kirim', 'topbar__ikon') ?> Pengiriman ke seluruh Indonesia</span>
    <span class="topbar__sep">•</span>
    <span><?= ikon('tangan', 'topbar__ikon') ?> Karya handmade dari Ponorogo</span>
    <span class="topbar__sep">•</span>
    <span><?= ikon('chat', 'topbar__ikon') ?> Bisa pesan custom</span>
  </div>
</div>

<header class="site-nav" id="siteNav">
  <div class="site-nav__inner">
    <a href="/toko-rajut/index.php" class="site-nav__brand">
      <img src="/toko-rajut/assets/img/logo bayuu.png" alt="" class="site-nav__logo">
      <span class="site-nav__brand-teks">Amoura <span>Atelier</span><small>Knit &amp; Craft Studio</small></span>
    </a>

    <button class="site-nav__toggle" id="navToggle" type="button" aria-label="Buka menu" aria-expanded="false">
      <?= ikon('menu') ?>
    </button>

    <nav class="site-nav__links" id="navLinks">
      <a href="/toko-rajut/index.php" class="<?= $halaman_aktif === 'beranda' ? 'active' : '' ?>">Beranda</a>
      <a href="/toko-rajut/produk.php" class="<?= $halaman_aktif === 'produk' ? 'active' : '' ?>">Produk</a>
      <a href="/toko-rajut/tentang.php" class="<?= $halaman_aktif === 'tentang' ? 'active' : '' ?>">Tentang</a>
      <a href="/toko-rajut/kontak.php" class="<?= $halaman_aktif === 'kontak' ? 'active' : '' ?>">Kontak</a>

      <span class="site-nav__garis" aria-hidden="true"></span>

      <a href="/toko-rajut/keranjang.php"
         class="site-nav__keranjang <?= $halaman_aktif === 'keranjang' ? 'active' : '' ?>"
         title="Keranjang belanja"
         aria-label="Keranjang belanja<?= $jumlahKeranjang > 0 ? ' (' . (int)$jumlahKeranjang . ' item)' : '' ?>">
        <?= ikon('keranjang') ?>
        <?php if ($jumlahKeranjang > 0): ?>
          <span class="site-nav__keranjang-badge"><?= (int)$jumlahKeranjang ?></span>
        <?php endif; ?>
      </a>

      <?php if (is_development()): ?>
        <?php if (is_admin_login()): ?>
          <a href="/toko-rajut/admin/index.php" class="admin-status">
            <span class="admin-status__dot"></span>
            Admin aktif
          </a>
        <?php else: ?>
          <a href="/toko-rajut/admin/login.php" class="site-nav__admin">Masuk Admin</a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="site-main">