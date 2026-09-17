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

$kategoriList = $pdo->query("
  SELECT kategori.id, kategori.nama, kategori.slug, COUNT(produk.id) AS jumlah
  FROM kategori
  LEFT JOIN produk ON produk.kategori_id = kategori.id
  GROUP BY kategori.id, kategori.nama, kategori.slug
  ORDER BY kategori.nama
")->fetchAll();

$totalProduk = (int)$pdo->query("SELECT COUNT(*) AS total FROM produk")->fetch()['total'];

include __DIR__ . '/includes/header.php';
?>

<section class="hero" data-aos="fade-up">
  <div class="hero__teks">
    <p class="hero__eyebrow"><?= ikon('bintang') ?> Dirajut dengan hati, untukmu</p>
    <h1>Setiap karya dibuat satu per satu, dengan ketelitian dan kehangatan</h1>
    <p class="lead">Amoura Atelier menghadirkan berbagai karya rajut yang dibuat perlahan dari benang pilihan. Setiap helai dirangkai dengan teliti, menciptakan karya yang hangat, lembut, dan penuh karakter.</p>
    <div class="hero__cta">
      <a href="produk.php" class="btn btn--solid">Belanja sekarang <?= ikon('panah') ?></a>
      <a href="tentang.php" class="btn btn--ghost">Kenali kami</a>
    </div>
    <div class="hero__stat">
      <div><strong><?= $totalProduk ?>+</strong><span>Produk siap kirim</span></div>
      <div><strong><?= count($kategoriList) ?></strong><span>Kategori pilihan</span></div>
      <div><strong>100%</strong><span>Buatan tangan</span></div>
    </div>
  </div>
  <div class="hero__visual">
    <img src="/toko-rajut/assets/img/logo bayuu.png" alt="Koleksi rajut Amoura Atelier">
    <div class="hero__tag">
      <?= ikon('tangan') ?>
      <span><strong>Handmade</strong>Madiun, Jawa Timur</span>
    </div>
  </div>
</section>

<section class="trust-strip" data-aos="fade-up">
  <div class="trust-strip__item"><?= ikon('kirim') ?><div><strong>Dikirim rapi</strong><span>Dikemas aman ke seluruh Indonesia</span></div></div>
  <div class="trust-strip__item"><?= ikon('tangan') ?><div><strong>Dirajut tangan</strong><span>Tanpa mesin, satu per satu</span></div></div>
  <div class="trust-strip__item"><?= ikon('perisai') ?><div><strong>Benang berkualitas</strong><span>Katun &amp; wol pilihan</span></div></div>
  <div class="trust-strip__item"><?= ikon('chat') ?><div><strong>Bisa custom</strong><span>Warna &amp; ukuran sesuai maumu</span></div></div>
</section>

<?php if (!empty($kategoriList)): ?>
<section class="section">
  <div class="section__head section__head--tengah">
    <span class="section__eyebrow">Kategori</span>
    <h2>Pilih sesuai kebutuhanmu</h2>
    <p>Dari pakaian hangat sampai pernak-pernik kecil yang menggemaskan.</p>
  </div>
  <div class="kategori-grid">
    <?php foreach ($kategoriList as $i => $k): ?>
      <a href="produk.php?kategori=<?= bersihkan($k['slug']) ?>" class="kategori-kartu" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
        <span class="kategori-kartu__nama"><?= bersihkan($k['nama']) ?></span>
        <span class="kategori-kartu__jumlah"><?= (int)$k['jumlah'] ?> produk</span>
        <span class="kategori-kartu__panah"><?= ikon('panah') ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="section__head section__head--baris">
    <div>
      <span class="section__eyebrow">Koleksi terbaru</span>
      <h2>Baru dirajut</h2>
      <p>Produk yang paling baru ditambahkan ke katalog.</p>
    </div>
    <a href="produk.php" class="section__tautan">Lihat semua produk <?= ikon('panah') ?></a>
  </div>
  <div class="product-grid">
    <?php foreach ($unggulan as $i => $p): ?>
      <div data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
        <?php $kembaliKe = 'index.php'; include __DIR__ . '/includes/kartu_produk.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <div class="langkah" data-aos="fade-up">
    <div class="section__head section__head--tengah">
      <span class="section__eyebrow">Cara belanja</span>
      <h2>Mudah, hanya tiga langkah</h2>
    </div>
    <div class="langkah__grid">
      <div class="langkah__item"><span>1</span><h3>Pilih produk</h3><p>Telusuri katalog, lalu tekan ikon keranjang pada produk yang kamu suka.</p></div>
      <div class="langkah__item"><span>2</span><h3>Isi data kirim</h3><p>Periksa keranjang, lanjut ke checkout dan lengkapi alamat pengiriman.</p></div>
      <div class="langkah__item"><span>3</span><h3>Pesanan dikerjakan</h3><p>Kami konfirmasi pesananmu dan mulai merajut dalam 2–5 hari kerja.</p></div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>