<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT produk.*, kategori.nama AS kategori_nama, kategori.slug AS kategori_slug
    FROM produk JOIN kategori ON kategori.id = produk.kategori_id
    WHERE produk.id = ?
");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    http_response_code(404);
    $page_title = 'Produk tidak ditemukan';
    $halaman_aktif = 'produk';
    include __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="empty-state">Produk yang Anda cari tidak ditemukan. <a href="produk.php">Kembali ke katalog</a>.</div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $produk['nama'];
$halaman_aktif = 'produk';
include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <div class="detail">
    <?php if (!empty($produk['gambar'])): ?>
      <div class="detail__swatch product-swatch--foto" style="background-image:url('/toko-rajut/assets/img/produk/<?= bersihkan($produk['gambar']) ?>')"></div>
    <?php else: ?>
      <div class="detail__swatch" style="background-color:<?= bersihkan($produk['warna']) ?>"></div>
    <?php endif; ?>
    <div>
      <a href="produk.php?kategori=<?= bersihkan($produk['kategori_slug']) ?>" class="detail__kategori"><?= bersihkan($produk['kategori_nama']) ?></a>
      <h1><?= bersihkan($produk['nama']) ?></h1>
      <p class="detail__harga"><?= rupiah((int)$produk['harga']) ?></p>
      <p class="detail__desc"><?= nl2br(bersihkan($produk['deskripsi'])) ?></p>
      <div class="detail__meta">
        <div><span>Stok tersedia</span><?= (int)$produk['stok'] ?> pcs</div>
        <div><span>Waktu pengerjaan</span>2–5 hari kerja</div>
        <div><span>Ditambahkan</span><?= date('d M Y', strtotime($produk['dibuat_pada'])) ?></div>
      </div>
      <a href="kontak.php" class="btn btn--solid">Tanya &amp; pesan produk ini</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>