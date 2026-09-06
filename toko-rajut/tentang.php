<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Tentang Kami';
$halaman_aktif = 'tentang';

$jumlahProduk = $pdo->query("SELECT COUNT(*) AS total FROM produk")->fetch()['total'];
$jumlahKategori = $pdo->query("SELECT COUNT(*) AS total FROM kategori")->fetch()['total'];

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <div class="section__head">
    <h2>Cerita di Balik Setiap Rajutan</h2>
  </div>
  <div class="tentang-grid">
    <div>
      <p>Amoura Atelier berawal dari kecintaan pada seni merajut yang dilakukan di waktu senggang, hingga tumbuh menjadi usaha rumahan di Madiun. Dari benang katun dan wol pilihan, setiap baju, sweater, tas, hingga mainan rajut dibuat dengan tangan, satu per satu, tanpa mesin.</p>
      <p>Kami percaya bahwa sesuatu yang dibuat dengan sabar akan memiliki cerita tersendiri. Karena itu, kami tidak mengejar jumlah, melainkan menjaga setiap simpul tetap rapi dan setiap jahitan dibuat dengan penuh perhatian, agar setiap karya terasa nyaman, hangat, dan dapat menemani keseharianmu.</p>
      <p>Melalui situs ini, kami ingin membawa setiap karya lebih dekat kepada kamu. Katalog kami diperbarui mengikuti ketersediaan produk, sehingga kamu dapat menemukan karya-karya yang masih tersedia dan menunggu untuk menjadi bagian dari ceritamu.</p>
    </div>
    <ul class="tentang-fakta">
      <li><span>Berdiri sejak</span>2026</li>
      <li><span>Lokasi</span>Madiun, Jawa Timur</li>
      <li><span>Kategori produk</span><?= (int)$jumlahKategori ?> kategori</li>
      <li><span>Produk aktif</span><?= (int)$jumlahProduk ?> produk</li>
      <li><span>Bahan utama</span>Katun &amp; wol</li>
    </ul>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>