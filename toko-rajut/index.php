<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Beranda';
$halaman_aktif = 'beranda';

$unggulan = $pdo->query("
  SELECT produk.*, kategori.nama AS kategori_nama
  FROM produk
  JOIN kategori ON kategori.id = produk.kategori_id
  ORDER BY produk.dibuat_pada DESC
  LIMIT 4
")->fetchAll();

$kategoriList = $pdo->query("SELECT id, nama, slug FROM kategori ORDER BY nama")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div>
    <p class="hero__eyebrow">Dirajut dengan hati, untukmu</p>
    <h1>Setiap karya dibuat satu per satu, dengan ketelitian dan kehangatan</h1>
    <p class="lead">Amoura Atelier menghadirkan berbagai karya rajut yang dibuat perlahan dari benang pilihan. Setiap helai dirangkai dengan teliti, menciptakan karya yang hangat, lembut, dan penuh karakter</p>
    <div class="hero__cta">
      <a href="produk.php" class="btn btn--solid">Lihat katalog</a>
      <a href="tentang.php" class="btn btn--ghost">Kenali kami</a>
    </div>
  </div>
<div class="hero__visual">
  <img src="/toko-rajut/assets/img/logo bayuu.png" alt="Logo Amoura Atelier">
</div>
</section>

<section class="section">
  <div class="section__head">
    <h2>Yang Membuat Amoura Berarti</h2>
    <p>Setiap karya dibuat dengan perhatian pada hal-hal kecil</p>
  </div>
  <div class="value-grid">
    <div class="value-tile">
      <span class="value-tile__num">01</span>
      <h3>Dirajut dengan tangan</h3>
      <p>Setiap karya dirangkai satu per satu dengan tangan, menghadirkan detail yang lebih personal dan penuh perhatian</p>
    </div>
    <div class="value-tile">
      <span class="value-tile__num">02</span>
      <h3>Benang pilihan</h3>
      <p>Kami memilih benang yang lembut dan berkualitas agar setiap karya terasa nyaman dan tetap indah digunakan</p>
    </div>
    <div class="value-tile">
      <span class="value-tile__num">03</span>
      <h3>Dibuat untukmu</h3>
      <p>Warna dan ukuran dapat disesuaikan sesuai kebutuhan, menjadikan setiap karya terasa lebih personal</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="section__head">
    <h2>Baru dirajut</h2>
    <p>Produk yang paling baru ditambahkan ke katalog.</p>
  </div>
  <div class="product-grid">
    <?php foreach ($unggulan as $p): ?>
      <article class="product-card">
       <?php if (!empty($p['gambar'])): ?>
          <div class="product-swatch product-swatch--foto" style="background-image:url('/toko-rajut/assets/img/produk/<?= bersihkan($p['gambar']) ?>')"></div>
       <?php else: ?>
          <div class="product-swatch" style="background-color:<?= bersihkan($p['warna']) ?>">
            <span class="product-swatch__initial"><?= bersihkan(mb_substr($p['nama'], 0, 1)) ?></span>
          </div>
        <?php endif; ?>
        <div class="product-card__body">
          <span class="product-card__kategori"><?= bersihkan($p['kategori_nama']) ?></span>
          <h3 class="product-card__nama"><?= bersihkan($p['nama']) ?></h3>
          <p class="product-card__harga"><?= rupiah((int)$p['harga']) ?></p>
        </div>
        <a href="detail.php?id=<?= (int)$p['id'] ?>" class="product-card__link">Lihat detail</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>