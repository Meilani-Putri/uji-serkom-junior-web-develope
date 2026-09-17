<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Pesanan Berhasil';
$halaman_aktif = 'keranjang';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    http_response_code(404);
    include __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="empty-state">Pesanan tidak ditemukan.</div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$stmtItem = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = ?");
$stmtItem->execute([$id]);
$itemList = $stmtItem->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <ol class="langkah-checkout">
    <li class="selesai"><span>1</span> Keranjang</li>
    <li class="selesai"><span>2</span> Data pengiriman</li>
    <li class="aktif"><span>3</span> Selesai</li>
  </ol>

  <div class="sukses-box">
    <span class="sukses-box__ikon"><?= ikon('centang') ?></span>
    <h2>Terima kasih, pesananmu sudah kami terima</h2>
    <p>Kode pesanan: <strong><?= bersihkan($pesanan['kode_pesanan']) ?></strong></p>
    <p class="sukses-box__catatan">Simpan kode ini. Kami akan menghubungimu lewat WhatsApp/email untuk konfirmasi.</p>
  </div>

  <div class="struk">
    <h2>Struk Pesanan</h2>
    <p><span>Nama</span><?= bersihkan($pesanan['nama_pembeli']) ?></p>
    <p><span>Alamat</span><?= bersihkan($pesanan['alamat_kirim']) ?></p>
    <p><span>Metode bayar</span><?= $pesanan['metode_bayar'] === 'cod' ? 'Bayar di Tempat (COD)' : 'Transfer Bank' ?></p>
    <p><span>Status</span><?= bersihkan(ucwords(str_replace('_', ' ', $pesanan['status']))) ?></p>

    <div class="keranjang-table-wrap">
      <table class="keranjang-table">
        <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr></thead>
        <tbody>
          <?php foreach ($itemList as $item): ?>
            <tr>
              <td><?= bersihkan($item['nama_produk']) ?></td>
              <td><?= rupiah((int)$item['harga_saat_beli']) ?></td>
              <td><?= (int)$item['jumlah'] ?></td>
              <td><?= rupiah((int)$item['subtotal']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="checkout-total">Total <strong><?= rupiah((int)$pesanan['total_harga']) ?></strong></p>
  </div>

  <div class="hero__cta">
    <a href="produk.php" class="btn btn--solid">Kembali belanja</a>
    <a href="kontak.php" class="btn btn--ghost btn--ikon"><?= ikon('chat') ?> Hubungi kami</a>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>