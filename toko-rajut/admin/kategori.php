<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Kategori Produk';
$halaman_admin = 'kategori';

$daftarKategori = $pdo->query("
    SELECT kategori.*, COUNT(produk.id) AS jumlah_produk
    FROM kategori
    LEFT JOIN produk ON produk.kategori_id = kategori.id
    GROUP BY kategori.id
    ORDER BY kategori.nama
")->fetchAll();

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Kategori produk</h2>
  <p>Total <?= count($daftarKategori) ?> kategori tersimpan di basis data.</p>
</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'tersimpan'): ?>
  <div class="alert alert--sukses">Kategori berhasil disimpan.</div>
<?php elseif (isset($_GET['status']) && $_GET['status'] === 'terhapus'): ?>
  <div class="alert alert--sukses">Kategori berhasil dihapus.</div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'masih_dipakai'): ?>
  <div class="alert alert--gagal">Kategori masih dipakai oleh produk. Pindahkan atau hapus dulu produk pada kategori ini sebelum menghapusnya.</div>
<?php endif; ?>

<div class="admin-toolbar">
  <a href="kategori_tambah.php" class="btn btn--solid">+ Tambah kategori</a>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
  <thead>
    <tr>
      <th>Nama</th><th>Slug</th><th>Jumlah produk</th><th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($daftarKategori as $k): ?>
      <tr>
        <td><?= bersihkan($k['nama']) ?></td>
        <td><?= bersihkan($k['slug']) ?></td>
        <td><?= (int)$k['jumlah_produk'] ?></td>
        <td class="admin-actions">
          <a href="kategori_edit.php?id=<?= (int)$k['id'] ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            Ubah
          </a>
          <a href="kategori_hapus.php?id=<?= (int)$k['id'] ?>" class="hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
            Hapus
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>