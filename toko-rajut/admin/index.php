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

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Daftar produk</h2>
  <p>Total <?= count($daftarProduk) ?> produk tersimpan di basis data.</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'tersimpan'): ?>
  <div class="alert alert--sukses">Produk berhasil disimpan.</div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'terhapus'): ?>
  <div class="alert alert--sukses">Produk berhasil dihapus.</div>
<?php endif; ?>

<table class="admin-table">
  <thead>
    <tr>
      <th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($daftarProduk as $p): ?>
      <tr>
        <td><?= bersihkan($p['nama']) ?></td>
        <td><?= bersihkan($p['kategori_nama']) ?></td>
        <td><?= rupiah((int)$p['harga']) ?></td>
        <td><?= (int)$p['stok'] ?></td>
        <td class="admin-actions">
          <a href="edit.php?id=<?= (int)$p['id'] ?>">Ubah</a>
          <a href="hapus.php?id=<?= (int)$p['id'] ?>" class="hapus">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>