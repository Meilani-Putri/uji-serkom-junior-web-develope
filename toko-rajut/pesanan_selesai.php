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

  <div class="struk" id="strukPelanggan">
    <div class="struk__brand">
      <strong>Amoura Atelier</strong>
      <span>Knit &amp; Craft Studio</span>
    </div>
    <p class="struk__kode">Kode Pesanan: <?= bersihkan($pesanan['kode_pesanan']) ?></p>
    <hr class="struk__garis">
    <h2>Struk Pesanan</h2>
    <p><span>Nama</span><?= bersihkan($pesanan['nama_pembeli']) ?></p>
    <p><span>Alamat</span><?= bersihkan($pesanan['alamat_kirim']) ?></p>
    <p><span>Metode bayar</span><?= $pesanan['metode_bayar'] === 'cod' ? 'Bayar di Tempat (COD)' : 'Transfer Bank' ?></p>
    <p><span>Status</span><?= bersihkan(ucwords(str_replace('_', ' ', $pesanan['status']))) ?></p>
    <hr class="struk__garis">

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
    <p class="struk__footer">Terima kasih telah berbelanja di Amoura Atelier.</p>
  </div>

  <div class="hero__cta">
    <button type="button" id="btnUnduhStrukPelanggan" class="btn btn--solid btn--ikon">Cetak Struk</button>
    <a href="produk.php" class="btn btn--ghost">Kembali ke Beranda</a>
    <a href="kontak.php" class="btn btn--ghost btn--ikon"><?= ikon('chat') ?> Hubungi kami</a>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
document.getElementById('btnUnduhStrukPelanggan').addEventListener('click', function () {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: 'mm', format: [80, 180] });

  let y = 10;
  doc.setFontSize(12);
  doc.text('Amoura Atelier', 40, y, { align: 'center' });
  y += 5;
  doc.setFontSize(8);
  doc.text('Knit & Craft Studio', 40, y, { align: 'center' });
  y += 6;
  doc.text('Kode: <?= addslashes(bersihkan($pesanan['kode_pesanan'])) ?>', 6, y);
  y += 5;
  doc.text('Nama: <?= addslashes(bersihkan($pesanan['nama_pembeli'])) ?>', 6, y);
  y += 5;
  doc.text('Metode: <?= $pesanan['metode_bayar'] === 'cod' ? 'COD' : 'Transfer Bank' ?>', 6, y);
  y += 3;

  doc.autoTable({
    startY: y,
    margin: { left: 6, right: 6 },
    styles: { fontSize: 7, cellPadding: 1.5 },
    head: [['Produk', 'Hrg', 'Qty', 'Subtotal']],
    body: [
      <?php foreach ($itemList as $item): ?>
      [
        '<?= addslashes(bersihkan($item['nama_produk'])) ?>',
        '<?= addslashes(rupiah((int)$item['harga_saat_beli'])) ?>',
        '<?= (int)$item['jumlah'] ?>',
        '<?= addslashes(rupiah((int)$item['subtotal'])) ?>'
      ],
      <?php endforeach; ?>
    ],
    theme: 'plain',
    headStyles: { fontStyle: 'bold' },
  });

  let yAkhir = doc.lastAutoTable.finalY + 5;
  doc.setFontSize(9);
  doc.text('Total: <?= addslashes(rupiah((int)$pesanan['total_harga'])) ?>', 6, yAkhir);
  yAkhir += 8;
  doc.setFontSize(7);
  doc.text('Terima kasih telah berbelanja', 40, yAkhir, { align: 'center' });

  doc.save('struk-<?= addslashes(bersihkan($pesanan['kode_pesanan'])) ?>.pdf');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>