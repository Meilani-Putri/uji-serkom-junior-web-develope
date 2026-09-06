<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Produk';
$halaman_aktif = 'produk';

$kategoriList = $pdo->query("SELECT id, nama, slug FROM kategori ORDER BY nama")->fetchAll();

$slugAktif = $_GET['kategori'] ?? '';
$kategoriTerpilih = null;
foreach ($kategoriList as $k) {
    if ($k['slug'] === $slugAktif) {
        $kategoriTerpilih = $k;
        break;
    }
}

if ($kategoriTerpilih) {
    $stmt = $pdo->prepare("
        SELECT produk.*, kategori.nama AS kategori_nama
        FROM produk JOIN kategori ON kategori.id = produk.kategori_id
        WHERE produk.kategori_id = ?
        ORDER BY produk.nama
    ");
    $stmt->execute([$kategoriTerpilih['id']]);
} else {
    $stmt = $pdo->query("
        SELECT produk.*, kategori.nama AS kategori_nama
        FROM produk JOIN kategori ON kategori.id = produk.kategori_id
        ORDER BY produk.nama
    ");
}
$daftarProduk = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <div class="section__head">
    <h2><?= $kategoriTerpilih ? bersihkan($kategoriTerpilih['nama']) : 'Semua produk' ?></h2>
    <p>Temukan rajutan lembut yang siap menemani keseharianmu</p>
  </div>

  <div class="kategori-bar">
    <a href="produk.php" class="<?= $kategoriTerpilih ? '' : 'active' ?>">Semua</a>
    <?php foreach ($kategoriList as $k): ?>
      <a href="produk.php?kategori=<?= bersihkan($k['slug']) ?>"
         class="<?= ($kategoriTerpilih && $kategoriTerpilih['slug'] === $k['slug']) ? 'active' : '' ?>">
        <?= bersihkan($k['nama']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($daftarProduk)): ?>
    <div class="empty-state">Belum ada produk di kategori ini.</div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($daftarProduk as $p): ?>
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
            <p class="product-card__stok">Stok: <?= (int)$p['stok'] ?></p>
          </div>
          <a href="detail.php?id=<?= (int)$p['id'] ?>" class="product-card__link">Lihat detail</a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>