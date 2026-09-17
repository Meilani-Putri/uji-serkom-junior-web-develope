<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Produk';
$halaman_aktif = 'produk';

$kategoriList = $pdo->query("SELECT id, nama, slug FROM kategori ORDER BY nama")->fetchAll();

$slugAktif  = $_GET['kategori'] ?? '';
$kataKunci  = trim($_GET['cari'] ?? '');
$urutan     = $_GET['urut'] ?? 'nama';

$kategoriTerpilih = null;
foreach ($kategoriList as $k) {
    if ($k['slug'] === $slugAktif) {
        $kategoriTerpilih = $k;
        break;
    }
}

// ---- Susun query: filter kategori & pencarian nama bisa dipakai bersamaan ----
$kondisi = [];
$parameter = [];

if ($kategoriTerpilih) {
    $kondisi[] = 'produk.kategori_id = ?';
    $parameter[] = $kategoriTerpilih['id'];
}

if ($kataKunci !== '') {
    $kondisi[] = 'produk.nama ILIKE ?';
    $parameter[] = '%' . $kataKunci . '%';
}

// ---- Pengurutan: daftar putih supaya aman dari SQL injection ----
$pilihanUrut = [
    'nama'       => ['label' => 'Nama A–Z',        'sql' => 'produk.nama ASC'],
    'terbaru'    => ['label' => 'Terbaru',          'sql' => 'produk.dibuat_pada DESC'],
    'termurah'   => ['label' => 'Harga termurah',   'sql' => 'produk.harga ASC'],
    'termahal'   => ['label' => 'Harga tertinggi',  'sql' => 'produk.harga DESC'],
];
if (!isset($pilihanUrut[$urutan])) {
    $urutan = 'nama';
}

$sql = "
    SELECT produk.*, kategori.nama AS kategori_nama
    FROM produk JOIN kategori ON kategori.id = produk.kategori_id
";
if (!empty($kondisi)) {
    $sql .= ' WHERE ' . implode(' AND ', $kondisi);
}
$sql .= ' ORDER BY ' . $pilihanUrut[$urutan]['sql'];

$stmt = $pdo->prepare($sql);
$stmt->execute($parameter);
$daftarProduk = $stmt->fetchAll();

// ---- Query string yang perlu dipertahankan di link kategori & tombol keranjang ----
$queryPertahankan = array_filter([
    'kategori' => $kategoriTerpilih['slug'] ?? null,
    'cari'     => $kataKunci !== '' ? $kataKunci : null,
    'urut'     => $urutan !== 'nama' ? $urutan : null,
]);
$queryStringSaatIni = $queryPertahankan ? '?' . http_build_query($queryPertahankan) : '';

// ---- Link tombol "Semua": buang filter kategori, pertahankan pencarian & urutan ----
$paramSemua = array_filter([
    'cari' => $kataKunci !== '' ? $kataKunci : null,
    'urut' => $urutan !== 'nama' ? $urutan : null,
]);
$querySemua = $paramSemua ? '?' . http_build_query($paramSemua) : '';

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
  <div class="page-head__inner">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="index.php">Beranda</a> <span>/</span>
      <?php if ($kategoriTerpilih): ?>
        <a href="produk.php">Produk</a> <span>/</span> <strong><?= bersihkan($kategoriTerpilih['nama']) ?></strong>
      <?php else: ?>
        <strong>Produk</strong>
      <?php endif; ?>
    </nav>
    <h1><?= $kategoriTerpilih ? bersihkan($kategoriTerpilih['nama']) : 'Semua produk' ?></h1>
    <p>Temukan rajutan lembut yang siap menemani keseharianmu.</p>
  </div>
</div>

<section class="section section--katalog">
  <div class="katalog-toolbar">
    <form method="get" action="produk.php" class="search-box" role="search">
      <?php if ($kategoriTerpilih): ?>
        <input type="hidden" name="kategori" value="<?= bersihkan($kategoriTerpilih['slug']) ?>">
      <?php endif; ?>
      <input type="hidden" name="urut" value="<?= bersihkan($urutan) ?>">
      <span class="search-box__icon" aria-hidden="true"><?= ikon('cari') ?></span>
      <input
        type="search"
        name="cari"
        value="<?= bersihkan($kataKunci) ?>"
        placeholder="Cari nama produk..."
        aria-label="Cari produk"
        class="search-box__input"
      >
      <button type="submit" class="search-box__submit">Cari</button>
    </form>

    <form method="get" action="produk.php" class="urut-box">
      <?php if ($kategoriTerpilih): ?>
        <input type="hidden" name="kategori" value="<?= bersihkan($kategoriTerpilih['slug']) ?>">
      <?php endif; ?>
      <?php if ($kataKunci !== ''): ?>
        <input type="hidden" name="cari" value="<?= bersihkan($kataKunci) ?>">
      <?php endif; ?>
      <label for="urut">Urutkan</label>
      <select name="urut" id="urut" onchange="this.form.submit()">
        <?php foreach ($pilihanUrut as $nilai => $opsi): ?>
          <option value="<?= $nilai ?>" <?= $urutan === $nilai ? 'selected' : '' ?>><?= $opsi['label'] ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <div class="kategori-bar">
    <a
      href="produk.php<?= bersihkan($querySemua) ?>"
      class="<?= $kategoriTerpilih ? '' : 'active' ?>"
    >
      Semua
    </a>
    <?php foreach ($kategoriList as $k): ?>
      <?php
        $paramKategori = ['kategori' => $k['slug']];
        if ($kataKunci !== '') {
            $paramKategori['cari'] = $kataKunci;
        }
        if ($urutan !== 'nama') {
            $paramKategori['urut'] = $urutan;
        }
      ?>
      <a
        href="produk.php?<?= http_build_query($paramKategori) ?>"
        class="<?= ($kategoriTerpilih && $kategoriTerpilih['slug'] === $k['slug']) ? 'active' : '' ?>"
      >
        <?= bersihkan($k['nama']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!empty($daftarProduk)): ?>
    <p class="katalog-hitung">
      Menampilkan <strong><?= count($daftarProduk) ?></strong> produk<?= $kataKunci !== '' ? ' untuk pencarian "' . bersihkan($kataKunci) . '"' : '' ?>.
    </p>
  <?php endif; ?>

  <?php if (empty($daftarProduk)): ?>
    <div class="empty-state">
      <?= ikon('cari', 'empty-state__ikon') ?>
      <?php if ($kataKunci !== ''): ?>
        <p>Tidak ada produk yang cocok dengan pencarian "<?= bersihkan($kataKunci) ?>".</p>
        <a href="produk.php<?= $kategoriTerpilih ? '?kategori=' . bersihkan($kategoriTerpilih['slug']) : '' ?>" class="btn btn--ghost">Hapus pencarian</a>
      <?php else: ?>
        <p>Belum ada produk di kategori ini.</p>
        <a href="produk.php" class="btn btn--ghost">Lihat semua produk</a>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($daftarProduk as $i => $p): ?>
        <div data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 60 ?>">
          <?php $kembaliKe = 'produk.php' . $queryStringSaatIni; include __DIR__ . '/includes/kartu_produk.php'; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>