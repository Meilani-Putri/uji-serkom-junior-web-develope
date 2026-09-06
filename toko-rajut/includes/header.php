<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$kategoriNav = $pdo->query("SELECT id, nama, slug FROM kategori ORDER BY nama")->fetchAll();
$halaman_aktif = $halaman_aktif ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? bersihkan($page_title) . ' — Amoura Atelier' : 'Amoura Atelier' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css">
</head>
<body>

<header class="site-nav">
  <a href="/toko-rajut/index.php" class="site-nav__brand">Amoura <span>Atelier</span></a>
  <nav class="site-nav__links">
    <a href="/toko-rajut/index.php" class="<?= $halaman_aktif === 'beranda' ? 'active' : '' ?>">Beranda</a>
    <a href="/toko-rajut/produk.php" class="<?= $halaman_aktif === 'produk' ? 'active' : '' ?>">Produk</a>
    <a href="/toko-rajut/tentang.php" class="<?= $halaman_aktif === 'tentang' ? 'active' : '' ?>">Tentang</a>
    <a href="/toko-rajut/kontak.php" class="<?= $halaman_aktif === 'kontak' ? 'active' : '' ?>">Kontak</a>
    <?php if (is_development()): ?>
  <?php if (is_admin_login()): ?>
    <a href="/toko-rajut/admin/index.php" class="admin-status">
      <span class="admin-status__dot"></span>
      Admin aktif — ke Dashboard
    </a>
  <?php else: ?>
    <a href="/toko-rajut/admin/login.php" class="site-nav__admin">Masuk Admin</a>
  <?php endif; ?>
<?php endif; ?>
  </nav>
</header>