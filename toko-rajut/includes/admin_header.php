<?php
$halaman_admin = $halaman_admin ?? '';

$tabAdmin = [
    'dashboard' => ['label' => 'Dashboard',  'href' => 'dashboard.php'],
    'produk'    => ['label' => 'Produk',     'href' => 'index.php'],
    'kategori'  => ['label' => 'Kategori',   'href' => 'kategori.php'],
    'pesanan'   => ['label' => 'Pesanan',    'href' => 'pesanan.php'],
    'laporan'   => ['label' => 'Laporan',    'href' => 'laporan.php'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? bersihkan($page_title) . ' — Admin Amoura Atelier' : 'Admin Amoura Atelier' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css?v=3">
</head>
<body class="admin-body">
<div class="admin-shell">

  <header class="admin-bar">
    <div class="admin-bar__inner">
      <a href="dashboard.php" class="admin-bar__brand">
        <span class="admin-bar__mark">AA</span>
        <span class="admin-bar__name">Amoura Atelier<small>Admin Panel</small></span>
      </a>

      <button class="admin-bar__toggle" id="adminNavToggle" type="button" aria-label="Buka menu admin" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
      </button>

      <div class="admin-bar__meta">
        <span class="admin-bar__date"><?= bersihkan(date('d F Y')) ?></span>
        <div class="admin-bar__user">
          <span class="admin-bar__avatar"><?= bersihkan(mb_strtoupper(mb_substr($_SESSION['admin_username'] ?? 'A', 0, 1))) ?></span>
          <span class="admin-bar__username"><?= bersihkan($_SESSION['admin_username'] ?? 'admin') ?></span>
        </div>
        <a href="/toko-rajut/index.php" class="admin-bar__link" title="Lihat situs publik">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
        <a href="logout.php" class="admin-bar__link admin-bar__link--keluar" title="Keluar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        </a>
      </div>
    </div>

    <nav class="admin-tabs" id="adminTabs">
      <?php foreach ($tabAdmin as $kunci => $tab): ?>
        <a href="<?= $tab['href'] ?>" class="<?= $halaman_admin === $kunci ? 'active' : '' ?>"><?= $tab['label'] ?></a>
      <?php endforeach; ?>
    </nav>
  </header>

  <main class="admin-main">
    <div class="admin-main__inner">