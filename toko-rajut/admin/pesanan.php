<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Pesanan';
$halaman_admin = 'pesanan';

$statusList = daftar_status_pesanan();

// ---- Filter ----
$statusAktif = $_GET['status'] ?? '';
$kataKunci   = trim($_GET['cari'] ?? '');

if (!array_key_exists($statusAktif, $statusList)) {
    $statusAktif = '';
}

$kondisi = [];
$parameter = [];

if ($statusAktif !== '') {
    $kondisi[] = 'status = ?';
    $parameter[] = $statusAktif;
}
if ($kataKunci !== '') {
    $kondisi[] = '(kode_pesanan ILIKE ? OR nama_pembeli ILIKE ? OR email_pembeli ILIKE ?)';
    $like = '%' . $kataKunci . '%';
    array_push($parameter, $like, $like, $like);
}

$sql = 'SELECT * FROM pesanan';
if (!empty($kondisi)) {
    $sql .= ' WHERE ' . implode(' AND ', $kondisi);
}
$sql .= ' ORDER BY dibuat_pada DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$daftarPesanan = $stmt->fetchAll();

// ---- Ringkasan cepat per status (dari seluruh data, bukan hasil filter) ----
$ringkasanStatus = $pdo->query("SELECT status, COUNT(*) AS total FROM pesanan GROUP BY status")->fetchAll();
$hitungStatus = array_fill_keys(array_keys($statusList), 0);
foreach ($ringkasanStatus as $r) {
    if (isset($hitungStatus[$r['status']])) {
        $hitungStatus[$r['status']] = (int)$r['total'];
    }
}
$totalSemua = array_sum($hitungStatus);

$flashPesanan = $_SESSION['flash_pesanan'] ?? null;
unset($_SESSION['flash_pesanan']);

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Pesanan masuk</h2>
  <p>Pantau transaksi pelanggan dan perbarui status pengiriman di sini.</p>
</div>

<?php if ($flashPesanan): ?>
  <div class="alert alert--<?= $flashPesanan['tipe'] === 'sukses' ? 'sukses' : 'gagal' ?>"><?= bersihkan($flashPesanan['teks']) ?></div>
<?php endif; ?>

<div class="admin-stats">
  <div class="admin-stat admin-stat--plum">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M12 20V4M20 20v-7"/></svg></span>
    <div><span class="admin-stat__label">Total pesanan</span><span class="admin-stat__value"><?= $totalSemua ?></span></div>
  </div>
  <div class="admin-stat admin-stat--gold">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg></span>
    <div><span class="admin-stat__label">Menunggu pembayaran</span><span class="admin-stat__value"><?= $hitungStatus['menunggu_pembayaran'] ?></span></div>
  </div>
  <div class="admin-stat admin-stat--blue">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/></svg></span>
    <div><span class="admin-stat__label">Diproses</span><span class="admin-stat__value"><?= $hitungStatus['diproses'] ?></span></div>
  </div>
  <div class="admin-stat admin-stat--sage">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7"/></svg></span>
    <div><span class="admin-stat__label">Selesai</span><span class="admin-stat__value"><?= $hitungStatus['selesai'] ?></span></div>
  </div>
</div>

<div class="filter-card">
  <form method="get" action="pesanan.php" class="laporan-filter">
    <label>Cari
      <input type="text" name="cari" value="<?= bersihkan($kataKunci) ?>" placeholder="Kode, nama, atau email...">
    </label>
    <label>Status
      <select name="status">
        <option value="">Semua status</option>
        <?php foreach ($statusList as $nilai => $label): ?>
          <option value="<?= $nilai ?>" <?= $statusAktif === $nilai ? 'selected' : '' ?>><?= $label ?> (<?= $hitungStatus[$nilai] ?>)</option>
        <?php endforeach; ?>
      </select>
    </label>
    <button type="submit" class="btn btn--solid">Tampilkan</button>
    <?php if ($statusAktif !== '' || $kataKunci !== ''): ?>
      <a href="pesanan.php" class="btn btn--ghost">Reset filter</a>
    <?php endif; ?>
  </form>
</div>

<?php if (empty($daftarPesanan)): ?>
  <div class="empty-state">Tidak ada pesanan yang cocok dengan filter ini.</div>
<?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Kode</th><th>Tanggal</th><th>Pembeli</th><th>Metode</th><th>Total</th><th>Status</th><th>Ubah status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($daftarPesanan as $p): ?>
          <tr>
            <td><strong><?= bersihkan($p['kode_pesanan']) ?></strong></td>
            <td><?= bersihkan(date('d/m/Y H:i', strtotime($p['dibuat_pada']))) ?></td>
            <td>
              <?= bersihkan($p['nama_pembeli']) ?><br>
              <span style="color:var(--admin-text-soft); font-size:.78rem;"><?= bersihkan($p['email_pembeli']) ?></span>
            </td>
            <td><?= $p['metode_bayar'] === 'cod' ? 'COD' : 'Transfer' ?></td>
            <td><?= rupiah((int)$p['total_harga']) ?></td>
            <td><?= badge_status($p['status']) ?></td>
            <td>
              <form method="post" action="pesanan_status.php" class="status-ubah">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="kembali" value="pesanan.php<?= (($statusAktif !== '' || $kataKunci !== '') ? '?' . http_build_query(array_filter(['status' => $statusAktif, 'cari' => $kataKunci])) : '') ?>">
                <select name="status">
                  <?php foreach ($statusList as $nilai => $label): ?>
                    <option value="<?= $nilai ?>" <?= $p['status'] === $nilai ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit">Simpan</button>
              </form>
            </td>
            <td class="admin-actions">
              <a href="struk.php?id=<?= (int)$p['id'] ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                Struk
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>