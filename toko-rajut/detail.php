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
    echo '<section class="section"><div class="empty-state"><p>Produk yang Anda cari tidak ditemukan.</p><a href="produk.php" class="btn btn--ghost">Kembali ke katalog</a></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Produk lain dari kategori yang sama
$stmtLain = $pdo->prepare("
    SELECT produk.*, kategori.nama AS kategori_nama
    FROM produk JOIN kategori ON kategori.id = produk.kategori_id
    WHERE produk.kategori_id = ? AND produk.id <> ?
    ORDER BY produk.dibuat_pada DESC
    LIMIT 4
");
$stmtLain->execute([$produk['kategori_id'], $produk['id']]);
$produkLain = $stmtLain->fetchAll();

$page_title = $produk['nama'];
$halaman_aktif = 'produk';
include __DIR__ . '/includes/header.php';
?>

<section class="section section--detail">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="index.php">Beranda</a> <span>/</span>
    <a href="produk.php">Produk</a> <span>/</span>
    <a href="produk.php?kategori=<?= bersihkan($produk['kategori_slug']) ?>"><?= bersihkan($produk['kategori_nama']) ?></a> <span>/</span>
    <strong><?= bersihkan($produk['nama']) ?></strong>
  </nav>

  <div class="detail" data-aos="fade-up">
    <div class="detail__galeri">
      <?php if (!empty($produk['gambar'])): ?>
        <div class="detail__swatch product-swatch--foto" style="background-image:url('/toko-rajut/assets/img/produk/<?= bersihkan($produk['gambar']) ?>')"></div>
      <?php else: ?>
        <div class="detail__swatch" style="background-color:<?= bersihkan($produk['warna']) ?>"></div>
      <?php endif; ?>
    </div>

    <div class="detail__info">
      <a href="produk.php?kategori=<?= bersihkan($produk['kategori_slug']) ?>" class="detail__kategori"><?= bersihkan($produk['kategori_nama']) ?></a>
      <h1><?= bersihkan($produk['nama']) ?></h1>

      <div class="detail__harga-baris">
        <p class="detail__harga"><?= rupiah((int)$produk['harga']) ?></p>
        <?php if ((int)$produk['stok'] > 0): ?>
          <span class="detail__label detail__label--ada"><?= ikon('centang') ?> Tersedia</span>
        <?php else: ?>
          <span class="detail__label detail__label--habis">Stok habis</span>
        <?php endif; ?>
      </div>

      <p class="detail__desc"><?= nl2br(bersihkan($produk['deskripsi'])) ?></p>

      <div class="detail__meta">
        <div><span>Stok tersedia</span><?= (int)$produk['stok'] ?> pcs</div>
        <div><span>Waktu pengerjaan</span>2–5 hari kerja</div>
        <div><span>Ditambahkan</span><?= date('d M Y', strtotime($produk['dibuat_pada'])) ?></div>
      </div>

      <?php if ((int)$produk['stok'] > 0): ?>
        <form method="post" action="tambah_keranjang.php" class="tambah-keranjang">
          <input type="hidden" name="produk_id" value="<?= (int)$produk['id'] ?>">
          <input type="hidden" name="kembali" value="detail.php?id=<?= (int)$produk['id'] ?>">
          <label class="tambah-keranjang__qty">
            Jumlah
            <input type="number" name="jumlah" value="1" min="1" max="<?= (int)$produk['stok'] ?>">
          </label>
          <button type="submit" class="btn btn--solid btn--ikon"><?= ikon('keranjang-plus') ?> Tambah ke Keranjang</button>
        </form>
      <?php else: ?>
        <p class="detail__stok-habis">Stok sedang habis. Hubungi kami untuk pemesanan ulang.</p>
      <?php endif; ?>

      <a href="kontak.php" class="btn btn--ghost btn--ikon"><?= ikon('chat') ?> Tanya dulu sebelum beli</a>

      <ul class="detail__jaminan">
        <li><?= ikon('tangan') ?> Dirajut tangan satu per satu</li>
        <li><?= ikon('perisai') ?> Benang katun &amp; wol pilihan</li>
        <li><?= ikon('kirim') ?> Dikemas rapi &amp; aman</li>
      </ul>
    </div>
  </div>
</section>

<?php if (!empty($produkLain)): ?>
<section class="section">
  <div class="section__head section__head--baris">
    <div>
      <span class="section__eyebrow">Mungkin kamu suka</span>
      <h2>Dari kategori yang sama</h2>
    </div>
    <a href="produk.php?kategori=<?= bersihkan($produk['kategori_slug']) ?>" class="section__tautan">Lihat semua <?= ikon('panah') ?></a>
  </div>
  <div class="product-grid">
    <?php foreach ($produkLain as $i => $p): ?>
      <div data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 60 ?>">
        <?php $kembaliKe = 'detail.php?id=' . (int)$produk['id']; include __DIR__ . '/includes/kartu_produk.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>