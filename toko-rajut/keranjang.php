<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/keranjang.php';

$page_title = 'Keranjang';
$halaman_aktif = 'keranjang';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    if ($_POST['aksi'] === 'update') {
        foreach ($_POST['jumlah'] ?? [] as $produkId => $jumlah) {
            keranjang_update((int)$produkId, (int)$jumlah);
        }
    } elseif ($_POST['aksi'] === 'hapus') {
        keranjang_hapus((int)($_POST['produk_id'] ?? 0));
    }
    header('Location: keranjang.php');
    exit;
}

$flash = $_SESSION['checkout_flash'] ?? null;
unset($_SESSION['checkout_flash']);

$isiKeranjang = keranjang_isi($pdo);
$total = keranjang_total($isiKeranjang);

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="index.php">Beranda</a> <span>/</span> <strong>Keranjang</strong>
  </nav>

  <div class="section__head">
    <h2>Keranjang Belanja</h2>
    <p>Periksa kembali pesananmu sebelum lanjut ke checkout.</p>
  </div>

  <ol class="langkah-checkout">
    <li class="aktif"><span>1</span> Keranjang</li>
    <li><span>2</span> Data pengiriman</li>
    <li><span>3</span> Selesai</li>
  </ol>

  <?php if ($flash && !empty($flash['errors'])): ?>
    <div class="alert alert--gagal">
      <?= implode('<br>', array_map('bersihkan', $flash['errors'])) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($isiKeranjang)): ?>
    <div class="empty-state">
      <?= ikon('keranjang', 'empty-state__ikon') ?>
      <p>Keranjangmu masih kosong.</p>
      <a href="produk.php" class="btn btn--solid">Mulai belanja</a>
    </div>
  <?php else: ?>

    <?php foreach ($isiKeranjang as $item): ?>
      <form method="post" action="keranjang.php" id="hapus-<?= (int)$item['produk']['id'] ?>">
        <input type="hidden" name="aksi" value="hapus">
        <input type="hidden" name="produk_id" value="<?= (int)$item['produk']['id'] ?>">
      </form>
    <?php endforeach; ?>

    <form method="post" action="keranjang.php" class="keranjang-form">
      <input type="hidden" name="aksi" value="update">
      <div class="keranjang-table-wrap" data-aos="fade-up">
        <table class="keranjang-table">
          <thead>
            <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($isiKeranjang as $item): ?>
              <tr>
                <td>
                  <div class="keranjang-produk">
                    <?php if (!empty($item['produk']['gambar'])): ?>
                      <span class="keranjang-produk__foto" style="background-image:url('/toko-rajut/assets/img/produk/<?= bersihkan($item['produk']['gambar']) ?>')"></span>
                    <?php else: ?>
                      <span class="keranjang-produk__foto" style="background-color:<?= bersihkan($item['produk']['warna']) ?>"></span>
                    <?php endif; ?>
                    <a href="detail.php?id=<?= (int)$item['produk']['id'] ?>"><?= bersihkan($item['produk']['nama']) ?></a>
                  </div>
                </td>
                <td><?= rupiah((int)$item['produk']['harga']) ?></td>
                <td>
                  <input
                    type="number"
                    name="jumlah[<?= (int)$item['produk']['id'] ?>]"
                    value="<?= (int)$item['jumlah'] ?>"
                    min="1"
                    max="<?= (int)$item['produk']['stok'] ?>"
                    class="keranjang-qty"
                    onchange="this.form.submit()"
                  >
                </td>
                <td><?= rupiah((int)$item['subtotal']) ?></td>
                <td>
                  <button type="submit" form="hapus-<?= (int)$item['produk']['id'] ?>" class="keranjang-hapus" aria-label="Hapus produk" title="Hapus dari keranjang"><?= ikon('sampah') ?></button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <div class="keranjang-ringkasan">
      <div>
        <p class="keranjang-ringkasan__label">Total belanja</p>
        <p>Total <strong><?= rupiah((int)$total) ?></strong></p>
      </div>
      <div class="keranjang-ringkasan__aksi">
        <a href="produk.php" class="btn btn--ghost">Lanjut belanja</a>
        <a href="checkout.php" class="btn btn--solid btn--ikon">Lanjut ke Checkout <?= ikon('panah') ?></a>
      </div>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>