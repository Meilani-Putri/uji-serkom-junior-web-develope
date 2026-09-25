<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Dashboard';
$halaman_admin = 'dashboard';

$totalProduk = (int)$pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPesanan = (int)$pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
$pendapatanBulanIni = (int)$pdo->query("
    SELECT COALESCE(SUM(total_harga),0) FROM pesanan
    WHERE date_trunc('month', dibuat_pada) = date_trunc('month', CURRENT_DATE)
")->fetchColumn();
$pesananHariIni = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE dibuat_pada::date = CURRENT_DATE")->fetchColumn();
$stokMenipis = (int)$pdo->query("SELECT COUNT(*) FROM produk WHERE stok > 0 AND stok <= 3")->fetchColumn();
$stokHabis = (int)$pdo->query("SELECT COUNT(*) FROM produk WHERE stok = 0")->fetchColumn();

// Pendapatan 7 hari terakhir, untuk grafik
$mulaiTanggal = date('Y-m-d', strtotime('-6 days'));
$stmtGrafik = $pdo->prepare("
    SELECT dibuat_pada::date AS tanggal, SUM(total_harga) AS total
    FROM pesanan
    WHERE dibuat_pada::date >= ?
    GROUP BY dibuat_pada::date
");
$stmtGrafik->execute([$mulaiTanggal]);
$hasilPerTanggal = [];
foreach ($stmtGrafik->fetchAll() as $row) {
    $hasilPerTanggal[$row['tanggal']] = (int)$row['total'];
}

$grafikLabel = [];
$grafikData = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $grafikLabel[] = date('d M', strtotime($tanggal));
    $grafikData[] = $hasilPerTanggal[$tanggal] ?? 0;
}

$transaksiTerbaru = $pdo->query("SELECT * FROM pesanan ORDER BY dibuat_pada DESC LIMIT 5")->fetchAll();
$produkStokRendah = $pdo->query("
    SELECT produk.nama, produk.stok, kategori.nama AS kategori_nama
    FROM produk JOIN kategori ON kategori.id = produk.kategori_id
    WHERE produk.stok <= 3
    ORDER BY produk.stok ASC
    LIMIT 5
")->fetchAll();

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Dashboard</h2>
  <p>Ringkasan produk dan transaksi toko Amoura Atelier.</p>
</div>

<div class="admin-stats">
  <div class="admin-stat admin-stat--plum">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M3 7l2.5-4h13L21 7"/></svg></span>
    <div>
      <span class="admin-stat__label">Total produk</span>
      <span class="admin-stat__value"><?= $totalProduk ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--gold">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M12 20V4M20 20v-7"/></svg></span>
    <div>
      <span class="admin-stat__label">Total pesanan</span>
      <span class="admin-stat__value"><?= $totalPesanan ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--sage">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
    <div>
      <span class="admin-stat__label">Pendapatan bulan ini</span>
      <span class="admin-stat__value" style="font-size:1.15rem;"><?= rupiah($pendapatanBulanIni) ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--rose">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg></span>
    <div>
      <span class="admin-stat__label">Pesanan hari ini</span>
      <span class="admin-stat__value"><?= $pesananHariIni ?></span>
    </div>
  </div>
</div>

<div class="dash-grid">
  <div class="dash-card dash-card--chart">
    <div class="dash-card__head">
      <h3>Pendapatan 7 Hari Terakhir</h3>
    </div>
    <canvas id="grafikPendapatan" height="130"></canvas>
  </div>

  <div class="dash-card">
    <div class="dash-card__head">
      <h3>Stok Menipis / Habis</h3>
      <a href="index.php">Kelola produk →</a>
    </div>
    <?php if (empty($produkStokRendah)): ?>
      <p class="dash-empty">Semua stok produk aman.</p>
    <?php else: ?>
      <ul class="dash-list">
        <?php foreach ($produkStokRendah as $p): ?>
          <li>
            <div>
              <strong><?= bersihkan($p['nama']) ?></strong>
              <span><?= bersihkan($p['kategori_nama']) ?></span>
            </div>
            <span class="stock-pill stock-pill--<?= (int)$p['stok'] === 0 ? 'habis' : 'menipis' ?>">
              <span class="stock-pill__dot"></span><?= (int)$p['stok'] === 0 ? 'Habis' : $p['stok'] . ' tersisa' ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<div class="dash-card" style="margin-top:20px;">
  <div class="dash-card__head">
    <h3>Transaksi Terbaru</h3>
    <a href="pesanan.php">Kelola pesanan →</a>  </div>
  <?php if (empty($transaksiTerbaru)): ?>
    <p class="dash-empty">Belum ada transaksi masuk.</p>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Kode</th><th>Pembeli</th><th>Tanggal</th><th>Status</th><th>Total</th><th>Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($transaksiTerbaru as $p): ?>
            <tr>
              <td><?= bersihkan($p['kode_pesanan']) ?></td>
              <td><?= bersihkan($p['nama_pembeli']) ?></td>
              <td><?= date('d M Y H:i', strtotime($p['dibuat_pada'])) ?></td>
              <td><?= badge_status($p['status']) ?></td>
              <td><?= rupiah((int)$p['total_harga']) ?></td>
              <td class="admin-actions"><a href="struk.php?id=<?= (int)$p['id'] ?>">Cetak</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('grafikPendapatan');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($grafikLabel) ?>,
    datasets: [{
      label: 'Pendapatan (Rp)',
      data: <?= json_encode($grafikData) ?>,
      backgroundColor: '#C6963C',
      borderRadius: 6,
      maxBarThickness: 42,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: (v) => 'Rp' + v.toLocaleString('id-ID') }
      }
    }
  }
});
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>