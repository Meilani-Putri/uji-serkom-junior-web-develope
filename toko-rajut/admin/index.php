<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Daftar Produk';
$halaman_admin = 'produk';

$daftarProduk = $pdo->query("
    SELECT produk.*, kategori.nama AS kategori_nama
    FROM produk JOIN kategori ON kategori.id = produk.kategori_id
    ORDER BY produk.id DESC
")->fetchAll();

$totalProduk = count($daftarProduk);
$totalStok = array_sum(array_column($daftarProduk, 'stok'));
$stokMenipis = count(array_filter($daftarProduk, fn($p) => (int)$p['stok'] > 0 && (int)$p['stok'] <= 3));
$stokHabis = count(array_filter($daftarProduk, fn($p) => (int)$p['stok'] === 0));

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Daftar produk</h2>
  <p>Total <?= $totalProduk ?> produk tersimpan di basis data.</p>
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
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9M14 17H5M17 3v8M8 13v8"/><circle cx="17" cy="7" r="3"/><circle cx="8" cy="17" r="3"/></svg></span>
    <div>
      <span class="admin-stat__label">Total stok</span>
      <span class="admin-stat__value"><?= $totalStok ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--sage">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5Z"/><path d="M3 7l9 5 9-5M12 22V12"/></svg></span>
    <div>
      <span class="admin-stat__label">Stok menipis</span>
      <span class="admin-stat__value"><?= $stokMenipis ?></span>
    </div>
  </div>
  <div class="admin-stat admin-stat--rose">
    <span class="admin-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg></span>
    <div>
      <span class="admin-stat__label">Stok habis</span>
      <span class="admin-stat__value"><?= $stokHabis ?></span>
    </div>
  </div>
</div>

<!-- Input Filter Tabel Admin -->
<div class="admin-toolbar">
  <div class="admin-search">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="text" id="adminSearchInput" class="admin-toolbar__search" placeholder="Cari di tabel produk..." onkeyup="cariTabelAdmin()">
  </div>
  <a href="tambah.php" class="btn btn--solid">+ Tambah produk</a>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'tersimpan'): ?>
  <div class="alert alert--sukses">Produk berhasil disimpan.</div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'terhapus'): ?>
  <div class="alert alert--sukses">Produk berhasil dihapus.</div>
<?php endif; ?>

<div class="admin-table-wrap">
<table class="admin-table" id="adminProductTable">
  <thead>
    <tr>
      <th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($daftarProduk as $p):
      $stok = (int)$p['stok'];
      if ($stok === 0) { $stokKelas = 'habis'; $stokLabel = 'Habis'; }
      elseif ($stok <= 3) { $stokKelas = 'menipis'; $stokLabel = $stok . ' — menipis'; }
      else { $stokKelas = 'aman'; $stokLabel = (string)$stok; }
    ?>
      <tr>
        <td><?= bersihkan($p['nama']) ?></td>
        <td><?= bersihkan($p['kategori_nama']) ?></td>
        <td><?= rupiah((int)$p['harga']) ?></td>
        <td><span class="stock-pill stock-pill--<?= $stokKelas ?>"><span class="stock-pill__dot"></span><?= bersihkan($stokLabel) ?></span></td>
        <td class="admin-actions">
          <a href="edit.php?id=<?= (int)$p['id'] ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            Ubah
          </a>
          <a href="hapus.php?id=<?= (int)$p['id'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus produk ini?')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
            Hapus
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<script>
function cariTabelAdmin() {
  const keyword = document.getElementById('adminSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#adminProductTable tbody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(keyword) ? '' : 'none';
  });
}
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>