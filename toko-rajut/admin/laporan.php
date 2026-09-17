<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Laporan Penjualan';
$halaman_admin = 'laporan';

function tanggalValid(string $tgl): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $tgl);
    return $d && $d->format('Y-m-d') === $tgl;
}

$tanggalMulai = $_GET['dari'] ?? date('Y-m-01');
$tanggalSelesai = $_GET['sampai'] ?? date('Y-m-d');

if (!tanggalValid($tanggalMulai)) {
    $tanggalMulai = date('Y-m-01');
}
if (!tanggalValid($tanggalSelesai)) {
    $tanggalSelesai = date('Y-m-d');
}
if ($tanggalMulai > $tanggalSelesai) {
    [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai, $tanggalMulai];
}

$stmt = $pdo->prepare("
    SELECT *
    FROM pesanan
    WHERE dibuat_pada::date BETWEEN ? AND ?
    ORDER BY dibuat_pada DESC
");
$stmt->execute([$tanggalMulai, $tanggalSelesai]);
$daftarPesanan = $stmt->fetchAll();

$totalPesanan = count($daftarPesanan);
$totalPendapatan = array_sum(array_column($daftarPesanan, 'total_harga'));

$stmtTerlaris = $pdo->prepare("
    SELECT pesanan_item.nama_produk,
           SUM(pesanan_item.jumlah) AS total_terjual,
           SUM(pesanan_item.subtotal) AS total_omzet
    FROM pesanan_item
    JOIN pesanan ON pesanan.id = pesanan_item.pesanan_id
    WHERE pesanan.dibuat_pada::date BETWEEN ? AND ?
    GROUP BY pesanan_item.nama_produk
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmtTerlaris->execute([$tanggalMulai, $tanggalSelesai]);
$produkTerlaris = $stmtTerlaris->fetchAll();

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Laporan penjualan</h2>
  <p>Ringkasan transaksi dari <?= bersihkan($tanggalMulai) ?> sampai <?= bersihkan($tanggalSelesai) ?>.</p>
</div>

<form method="get" action="laporan.php" class="laporan-filter">
  <label>Dari tanggal
    <input type="date" name="dari" value="<?= bersihkan($tanggalMulai) ?>">
  </label>
  <label>Sampai tanggal
    <input type="date" name="sampai" value="<?= bersihkan($tanggalSelesai) ?>">
  </label>
  <button type="submit" class="btn btn--solid">Tampilkan</button>
</form>

<div class="admin-stats">
  <div class="admin-stat admin-stat--plum">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20.4 6.6 8 19l-4.4-4.4"/><path d="m20.4 6.6-8 8"/></svg></span>
    <div>
      <span class="admin-stat__label">Total pesanan</span>
      <span class="admin-stat__value"><?= (int)$totalPesanan ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--gold">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
    <div>
      <span class="admin-stat__label">Total pendapatan</span>
      <span class="admin-stat__value"><?= rupiah((int)$totalPendapatan) ?></span>
    </div>
  </div>
</div>

<h3 class="form-section__title laporan-blok">Produk terlaris</h3>
<?php if (empty($produkTerlaris)): ?>
  <div class="empty-state">Belum ada penjualan pada rentang tanggal ini.</div>
<?php else: ?>
  <div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>Nama produk</th><th>Jumlah terjual</th><th>Total omzet</th></tr>
    </thead>
    <tbody>
      <?php foreach ($produkTerlaris as $p): ?>
        <tr>
          <td><?= bersihkan($p['nama_produk']) ?></td>
          <td><?= (int)$p['total_terjual'] ?></td>
          <td><?= rupiah((int)$p['total_omzet']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>

<h3 class="form-section__title laporan-blok">Daftar transaksi</h3>
<?php if (empty($daftarPesanan)): ?>
  <div class="empty-state">Belum ada transaksi pada rentang tanggal ini.</div>
<?php else: ?>
  <div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr><th>Kode pesanan</th><th>Tanggal</th><th>Pembeli</th><th>Total</th><th>Status</th></tr>
    </thead>
    <tbody>
      <?php foreach ($daftarPesanan as $p): ?>
        <tr>
          <td><?= bersihkan($p['kode_pesanan']) ?></td>
          <td><?= bersihkan(date('d/m/Y H:i', strtotime($p['dibuat_pada']))) ?></td>
          <td><?= bersihkan($p['nama_pembeli']) ?></td>
          <td><?= rupiah((int)$p['total_harga']) ?></td>
          <td><?= bersihkan(ucwords(str_replace('_', ' ', $p['status']))) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>