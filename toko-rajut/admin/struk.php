<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Struk Transaksi';
$halaman_admin = 'pesanan';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->execute([$id]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header('Location: pesanan.php?error=' . urlencode('Transaksi tidak ditemukan.'));
    exit;
}

$stmtItem = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = ?");
$stmtItem->execute([$id]);
$itemList = $stmtItem->fetchAll();

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Struk — <?= bersihkan($pesanan['kode_pesanan']) ?></h2>
  <p>Detail transaksi dan unduh invoice untuk pelanggan.</p>
</div>

<a href="pesanan.php" class="text-link" style="display:inline-flex; align-items:center; gap:6px; margin-bottom:20px; font-weight:700; color:var(--admin-gold);">&larr; Kembali ke daftar pesanan</a>

<div class="struk" id="strukAdmin">
  <div class="struk__brand">
    <strong>Amoura Atelier</strong>
    <span>Knit &amp; Craft Studio</span>
  </div>
  <p class="struk__kode">Kode Pesanan: <?= bersihkan($pesanan['kode_pesanan']) ?></p>
  <hr class="struk__garis">
  <p><span>Nama</span><?= bersihkan($pesanan['nama_pembeli']) ?></p>
  <p><span>Email</span><?= bersihkan($pesanan['email_pembeli']) ?></p>
  <p><span>Telepon</span><?= bersihkan($pesanan['telepon_pembeli']) ?></p>
  <p><span>Alamat</span><?= bersihkan($pesanan['alamat_kirim']) ?></p>
  <p><span>Metode bayar</span><?= $pesanan['metode_bayar'] === 'cod' ? 'Bayar di Tempat (COD)' : 'Transfer Bank' ?></p>
  <p><span>Status</span><?= badge_status($pesanan['status']) ?></p>
  <p><span>Tanggal</span><?= date('d F Y, H:i', strtotime($pesanan['dibuat_pada'])) ?></p>

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

<button type="button" id="btnUnduhStruk" class="btn btn--solid btn--ikon" style="margin-top:20px;">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px;"><path d="M12 3v12m0 0-4-4m4 4 4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
  Unduh Struk PDF
</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById('btnUnduhStruk').addEventListener('click', function () {
  const { jsPDF } = window.jspdf;

  // ---- Data dari server ----
  const kode      = '<?= addslashes(bersihkan($pesanan['kode_pesanan'])) ?>';
  const tanggal   = '<?= addslashes(date('d-m-Y', strtotime($pesanan['dibuat_pada']))) ?>';
  const jam       = '<?= addslashes(date('H:i:s', strtotime($pesanan['dibuat_pada']))) ?>';
  const nama      = '<?= addslashes(bersihkan($pesanan['nama_pembeli'])) ?>';
  const alamat    = '<?= addslashes(bersihkan($pesanan['alamat_kirim'])) ?>';
  const metode    = '<?= addslashes($pesanan['metode_bayar'] === 'cod' ? 'COD (Bayar di tempat)' : 'Transfer Bank') ?>';
  const statusTxt = '<?= addslashes(daftar_status_pesanan()[$pesanan['status']] ?? $pesanan['status']) ?>';
  const total     = <?= (int)$pesanan['total_harga'] ?>;
  const items = [
    <?php foreach ($itemList as $item): ?>
    {
      nama: '<?= addslashes(bersihkan($item['nama_produk'])) ?>',
      harga: <?= (int)$item['harga_saat_beli'] ?>,
      qty: <?= (int)$item['jumlah'] ?>,
      subtotal: <?= (int)$item['subtotal'] ?>,
    },
    <?php endforeach; ?>
  ];

  const fmtRp = (n) => 'Rp' + Number(n).toLocaleString('id-ID');
  const totalQty = items.reduce((a, it) => a + it.qty, 0);

  // ---- Ukuran kertas struk (mirip thermal 80mm), tinggi menyesuaikan isi ----
  const W = 80;
  const marginX = 6;
  const lebarIsi = W - marginX * 2;
  let tinggiPerkiraan = 78; // header + ikon + meta
  items.forEach((it) => {
    const barisNama = Math.ceil(it.nama.length / 30) || 1;
    tinggiPerkiraan += barisNama * 4.2 + 4.6;
  });
  tinggiPerkiraan += 34; // total + footer

  const doc = new jsPDF({ unit: 'mm', format: [W, tinggiPerkiraan] });
  const PLUM = [75, 46, 66];
  const INK  = [42, 36, 32];
  const SOFT = [107, 97, 85];
  const pageW = doc.internal.pageSize.getWidth();
  const tengah = pageW / 2;

  const garisPutus = (y) => {
    doc.setDrawColor(190, 178, 164);
    doc.setLineWidth(0.15);
    let x = marginX;
    while (x < pageW - marginX) {
      doc.line(x, y, Math.min(x + 1.4, pageW - marginX), y);
      x += 2.4;
    }
  };

  let y = 8;

  // ---- Ikon gulungan benang rajut sederhana, di tengah atas ----
  doc.setDrawColor(...PLUM);
  doc.setLineWidth(0.5);
  doc.circle(tengah, y + 5, 5.2, 'S');
  doc.setLineWidth(0.35);
  doc.line(tengah - 4.6, y + 1.6, tengah + 4.6, y + 8.4);
  doc.line(tengah - 4.6, y + 8.4, tengah + 4.6, y + 1.6);
  doc.line(tengah - 5.2, y + 5, tengah + 5.2, y + 5);
  y += 15;

  // ---- Nama toko & kontak, center ----
  doc.setTextColor(...PLUM);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(13);
  doc.text('Amoura Atelier', tengah, y, { align: 'center' });
  y += 5;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);
  doc.setTextColor(...SOFT);
  doc.text('Knit & Craft Studio', tengah, y, { align: 'center' });
  y += 4.4;
  doc.text('Madiun, Jawa Timur', tengah, y, { align: 'center' });
  y += 4;
  doc.text('WA 085607153907', tengah, y, { align: 'center' });
  y += 6;

  garisPutus(y);
  y += 5;

  // ---- Meta transaksi ----
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);
  doc.setTextColor(...INK);
  doc.text(tanggal, marginX, y);
  doc.text(jam, marginX, y + 4);
  doc.text(nama, pageW - marginX, y, { align: 'right' });
  doc.setTextColor(...SOFT);
  const alamatWrap = doc.splitTextToSize(alamat, 34);
  doc.text(alamatWrap, pageW - marginX, y + 4, { align: 'right' });
  y += 4 + Math.max(alamatWrap.length, 1) * 3.6 + 2;

  doc.setTextColor(...INK);
  doc.text('No. ' + kode, marginX, y);
  y += 3;

  garisPutus(y);
  y += 5;

  // ---- Daftar item, bernomor seperti struk kasir ----
  doc.setFontSize(8);
  items.forEach((it, i) => {
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(...INK);
    const baris = doc.splitTextToSize(`${i + 1}. ${it.nama}`, lebarIsi);
    doc.text(baris, marginX, y);
    y += baris.length * 4.2;

    doc.setFont('helvetica', 'normal');
    doc.setTextColor(...SOFT);
    doc.text(`${it.qty} x ${fmtRp(it.harga)}`, marginX + 3, y);
    doc.setTextColor(...INK);
    doc.text(fmtRp(it.subtotal), pageW - marginX, y, { align: 'right' });
    y += 5.2;
  });

  garisPutus(y);
  y += 5;

  // ---- Ringkasan & total ----
  doc.setFontSize(8);
  doc.setFont('helvetica', 'normal');
  doc.setTextColor(...SOFT);
  doc.text('Total Qty', marginX, y);
  doc.setTextColor(...INK);
  doc.text(String(totalQty), pageW - marginX, y, { align: 'right' });
  y += 4.6;

  doc.setTextColor(...SOFT);
  doc.text('Metode Bayar', marginX, y);
  doc.setTextColor(...INK);
  doc.text(metode, pageW - marginX, y, { align: 'right' });
  y += 4.6;

  doc.setTextColor(...SOFT);
  doc.text('Status', marginX, y);
  doc.setTextColor(...INK);
  doc.text(statusTxt, pageW - marginX, y, { align: 'right' });
  y += 6;

  garisPutus(y);
  y += 6;

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(11);
  doc.setTextColor(...PLUM);
  doc.text('TOTAL', marginX, y);
  doc.text(fmtRp(total), pageW - marginX, y, { align: 'right' });
  y += 8;

  garisPutus(y);
  y += 6;

  // ---- Footer ----
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(8);
  doc.setTextColor(...PLUM);
  doc.text('Terima Kasih Telah Berbelanja', tengah, y, { align: 'center' });
  y += 4.4;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(7);
  doc.setTextColor(...SOFT);
  doc.text('meilani1856@gmail.com', tengah, y, { align: 'center' });

  doc.save('struk-' + kode + '.pdf');
});
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>