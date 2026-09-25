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
$kategoriId = (int)($_GET['kategori_id'] ?? 0);

if (!tanggalValid($tanggalMulai)) {
    $tanggalMulai = date('Y-m-01');
}
if (!tanggalValid($tanggalSelesai)) {
    $tanggalSelesai = date('Y-m-d');
}
if ($tanggalMulai > $tanggalSelesai) {
    [$tanggalMulai, $tanggalSelesai] = [$tanggalSelesai, $tanggalMulai];
}

$daftarKategori = $pdo->query("SELECT id, nama FROM kategori ORDER BY nama")->fetchAll();

$filterKategoriSql = '';
$parameter = [$tanggalMulai, $tanggalSelesai];
if ($kategoriId > 0) {
    $filterKategoriSql = "
        AND EXISTS (
            SELECT 1 FROM pesanan_item
            JOIN produk ON produk.id = pesanan_item.produk_id
            WHERE pesanan_item.pesanan_id = pesanan.id
              AND produk.kategori_id = ?
        )
    ";
    $parameter[] = $kategoriId;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM pesanan
    WHERE dibuat_pada::date BETWEEN ? AND ?
    $filterKategoriSql
    ORDER BY dibuat_pada DESC
");
$stmt->execute($parameter);
$daftarPesanan = $stmt->fetchAll();

$totalPesanan = count($daftarPesanan);
$totalPendapatan = array_sum(array_column($daftarPesanan, 'total_harga'));

$filterKategoriTerlarisSql = '';
$parameterTerlaris = [$tanggalMulai, $tanggalSelesai];
if ($kategoriId > 0) {
    $filterKategoriTerlarisSql = 'AND produk.kategori_id = ?';
    $parameterTerlaris[] = $kategoriId;
}

$stmtTerlaris = $pdo->prepare("
    SELECT pesanan_item.nama_produk,
           SUM(pesanan_item.jumlah) AS total_terjual,
           SUM(pesanan_item.subtotal) AS total_omzet
    FROM pesanan_item
    JOIN pesanan ON pesanan.id = pesanan_item.pesanan_id
    JOIN produk ON produk.id = pesanan_item.produk_id
    WHERE pesanan.dibuat_pada::date BETWEEN ? AND ?
    $filterKategoriTerlarisSql
    GROUP BY pesanan_item.nama_produk
    ORDER BY total_terjual DESC
    LIMIT 5
");
$stmtTerlaris->execute($parameterTerlaris);
$produkTerlaris = $stmtTerlaris->fetchAll();

$namaKategoriTerpilih = '';
foreach ($daftarKategori as $k) {
    if ((int)$k['id'] === $kategoriId) {
        $namaKategoriTerpilih = $k['nama'];
        break;
    }
}

$flashPesanan = $_SESSION['flash_pesanan'] ?? null;
unset($_SESSION['flash_pesanan']);

include __DIR__ . '/../includes/admin_header.php';
?>

<?php if ($flashPesanan): ?>
  <div class="alert alert--<?= $flashPesanan['tipe'] === 'sukses' ? 'sukses' : 'gagal' ?>"><?= bersihkan($flashPesanan['teks']) ?></div>
<?php endif; ?>

<div class="section__head">
  <h2>Laporan penjualan</h2>
  <p>
    Ringkasan transaksi dari <?= bersihkan($tanggalMulai) ?> sampai <?= bersihkan($tanggalSelesai) ?>
    <?= $namaKategoriTerpilih !== '' ? ' — kategori ' . bersihkan($namaKategoriTerpilih) : '' ?>.
  </p>
</div>

<div class="filter-card">
<form method="get" action="laporan.php" class="laporan-filter">
  <label>Dari tanggal
    <input type="date" name="dari" value="<?= bersihkan($tanggalMulai) ?>">
  </label>
  <label>Sampai tanggal
    <input type="date" name="sampai" value="<?= bersihkan($tanggalSelesai) ?>">
  </label>
  <label>Kategori
    <select name="kategori_id">
      <option value="0">Semua kategori</option>
      <?php foreach ($daftarKategori as $k): ?>
        <option value="<?= (int)$k['id'] ?>" <?= $kategoriId === (int)$k['id'] ? 'selected' : '' ?>><?= bersihkan($k['nama']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button type="submit" class="btn btn--solid">Tampilkan</button>
  <button type="button" id="btnCetakLaporan" class="btn btn--ghost">Unduh PDF</button>
</form>
</div>

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
  <div class="empty-state">Belum ada penjualan pada rentang ini.</div>
<?php else: ?>
  <div class="admin-table-wrap">
  <table class="admin-table" id="tabelTerlaris">
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
  <div class="empty-state">Belum ada transaksi pada rentang ini.</div>
<?php else: ?>
  <div class="admin-table-wrap">
  <table class="admin-table" id="tabelTransaksi">
    <thead>
      <tr><th>Kode pesanan</th><th>Tanggal</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Ubah status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($daftarPesanan as $p): ?>
        <tr>
          <td><?= bersihkan($p['kode_pesanan']) ?></td>
          <td><?= bersihkan(date('d/m/Y H:i', strtotime($p['dibuat_pada']))) ?></td>
          <td><?= bersihkan($p['nama_pembeli']) ?></td>
          <td><?= rupiah((int)$p['total_harga']) ?></td>
          <td><?= badge_status($p['status']) ?></td>
          <td>
            <form method="post" action="pesanan_status.php" class="status-ubah">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <input type="hidden" name="kembali" value="laporan.php?<?= http_build_query($_GET) ?>">
              <select name="status">
                <?php foreach (daftar_status_pesanan() as $nilai => $label): ?>
                  <option value="<?= $nilai ?>" <?= $p['status'] === $nilai ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit">Simpan</button>
            </form>
          </td>
          <td class="admin-actions"><a href="struk.php?id=<?= (int)$p['id'] ?>">Cetak</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
document.getElementById('btnCetakLaporan').addEventListener('click', function () {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  doc.setFontSize(15);
  doc.text('Laporan Penjualan — Amoura Atelier', 14, 18);
  doc.setFontSize(10);
  doc.text('Periode: <?= bersihkan($tanggalMulai) ?> s/d <?= bersihkan($tanggalSelesai) ?><?= $namaKategoriTerpilih !== '' ? ' — Kategori: ' . bersihkan($namaKategoriTerpilih) : '' ?>', 14, 25);
  doc.text('Total pesanan: <?= (int)$totalPesanan ?>   Total pendapatan: <?= bersihkan(rupiah((int)$totalPendapatan)) ?>', 14, 31);

  doc.autoTable({
    startY: 38,
    head: [['Produk Terlaris', 'Jumlah Terjual', 'Omzet']],
    body: [
      <?php foreach ($produkTerlaris as $p): ?>
      ['<?= addslashes(bersihkan($p['nama_produk'])) ?>', '<?= (int)$p['total_terjual'] ?>', '<?= addslashes(rupiah((int)$p['total_omzet'])) ?>'],
      <?php endforeach; ?>
    ],
    theme: 'grid',
    headStyles: { fillColor: [75, 46, 66] },
  });

  doc.autoTable({
    startY: doc.lastAutoTable.finalY + 12,
    head: [['Kode', 'Tanggal', 'Pembeli', 'Total', 'Status']],
    body: [
      <?php foreach ($daftarPesanan as $p): ?>
      [
        '<?= addslashes(bersihkan($p['kode_pesanan'])) ?>',
        '<?= addslashes(date('d/m/Y H:i', strtotime($p['dibuat_pada']))) ?>',
        '<?= addslashes(bersihkan($p['nama_pembeli'])) ?>',
        '<?= addslashes(rupiah((int)$p['total_harga'])) ?>',
        '<?= addslashes(bersihkan(ucwords(str_replace('_',' ',$p['status'])))) ?>'
      ],
      <?php endforeach; ?>
    ],
    theme: 'grid',
    headStyles: { fillColor: [75, 46, 66] },
  });

  doc.save('laporan-penjualan-<?= bersihkan($tanggalMulai) ?>_<?= bersihkan($tanggalSelesai) ?>.pdf');
});
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>