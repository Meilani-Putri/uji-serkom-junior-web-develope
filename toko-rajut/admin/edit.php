<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Ubah Produk';
$halaman_admin = 'produk';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$produk = $stmt->fetch();

if (!$produk) {
    header('Location: index.php');
    exit;
}

$kategoriList = $pdo->query("SELECT id, nama FROM kategori ORDER BY nama")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategoriId = (int)($_POST['kategori_id'] ?? 0);
    $harga = (int)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    $warna = trim($_POST['warna'] ?? '');
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $warna)) {
        $warna = '#7C9473';
    }

    if ($nama === '' || $kategoriId === 0 || $harga <= 0) {
        $error = 'Nama, kategori, dan harga (lebih dari 0) wajib diisi.';
        $produk = array_merge($produk, $_POST);
    } else {
        $namaFileGambar = $produk['gambar'];

        if (!empty($_FILES['gambar']['name'])) {
            $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
            $ekstensi = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

            if (!in_array($ekstensi, $ekstensiValid)) {
                $error = 'Format foto harus JPG, PNG, atau WEBP.';
            } elseif ($_FILES['gambar']['size'] > 3 * 1024 * 1024) {
                $error = 'Ukuran foto maksimal 3MB.';
            } else {
                $namaFileGambar = buat_slug($nama) . '-' . substr(uniqid(), -6) . '.' . $ekstensi;
                $tujuan = __DIR__ . '/../assets/img/produk/' . $namaFileGambar;
                move_uploaded_file($_FILES['gambar']['tmp_name'], $tujuan);
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("
                UPDATE produk SET kategori_id=?, nama=?, harga=?, stok=?, warna=?, deskripsi=?, gambar=?
                WHERE id=?
            ");
            $stmt->execute([$kategoriId, $nama, $harga, $stok, $warna, $deskripsi, $namaFileGambar, $id]);
            header('Location: index.php?status=tersimpan');
            exit;
        }
    }
}

include __DIR__ . '/../includes/admin_header.php';

$warnaPreview = preg_match('/^#[0-9A-Fa-f]{6}$/', $produk['warna']) ? $produk['warna'] : '#7C9473';
$gambarPreviewUrl = !empty($produk['gambar']) ? '/toko-rajut/assets/img/produk/' . $produk['gambar'] : '';
?>

<div class="section__head">
  <h2>Ubah produk</h2>
  <p>Perbarui detail produk <?= bersihkan($produk['nama']) ?>.</p>
</div>

<?php if ($error): ?><div class="alert alert--gagal"><?= bersihkan($error) ?></div><?php endif; ?>

<form class="admin-form" method="post" action="edit.php?id=<?= (int)$id ?>" enctype="multipart/form-data">
  <div class="admin-form__main">

    <div class="form-section">
      <h3 class="form-section__title">Informasi dasar</h3>
      <div class="form-group">
        <label for="nama">Nama produk</label>
        <input type="text" id="nama" name="nama" value="<?= bersihkan($produk['nama']) ?>" required>
      </div>
      <div class="form-group">
        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required>
          <?php foreach ($kategoriList as $k): ?>
            <option value="<?= (int)$k['id'] ?>" <?= $k['id'] == $produk['kategori_id'] ? 'selected' : '' ?>><?= bersihkan($k['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-section">
      <h3 class="form-section__title">Harga &amp; stok</h3>
      <div class="form-row">
        <div class="form-group">
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" min="1" value="<?= (int)$produk['harga'] ?>" required>
        </div>
        <div class="form-group">
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" min="0" value="<?= (int)$produk['stok'] ?>" required>
        </div>
      </div>
    </div>

    <div class="form-section">
      <h3 class="form-section__title">Deskripsi</h3>
      <div class="form-group">
        <label for="deskripsi">Ceritakan detail produk ini</label>
        <textarea id="deskripsi" name="deskripsi" rows="5"><?= bersihkan($produk['deskripsi']) ?></textarea>
      </div>
    </div>

    <button type="submit" class="btn btn--solid">Simpan perubahan</button>
  </div>

  <aside class="admin-form__side">
    <div class="form-section">
      <h3 class="form-section__title">Tampilan produk</h3>
      <p class="form-section__hint">Kartu warna dipakai kalau belum ada foto. Kalau foto diunggah, foto yang akan tampil.</p>

      <div class="preview-swatch" id="previewSwatch"
        style="<?= $gambarPreviewUrl ? "background-image:url('{$gambarPreviewUrl}'); background-size:cover; background-position:center;" : "background:{$warnaPreview};" ?>">
      </div>

      <div class="form-group">
        <label for="warna">Warna kartu</label>
        <div class="color-input">
          <input type="color" id="warnaPicker" value="<?= bersihkan($warnaPreview) ?>">
          <input type="text" id="warna" name="warna" value="<?= bersihkan($warnaPreview) ?>" maxlength="7">
        </div>
      </div>
      <div class="form-group">
        <label for="gambar">Ganti foto (opsional, kosongkan jika tidak ingin ganti)</label>
        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>
  </aside>
</form>

<script>
  const previewSwatch = document.getElementById('previewSwatch');
  const warnaPicker = document.getElementById('warnaPicker');
  const warnaText = document.getElementById('warna');
  const gambarInput = document.getElementById('gambar');

  function tampilkanWarna(nilai) {
    previewSwatch.style.backgroundImage = 'none';
    previewSwatch.style.backgroundColor = nilai;
  }

  warnaPicker.addEventListener('input', () => {
    warnaText.value = warnaPicker.value;
    tampilkanWarna(warnaPicker.value);
  });

  warnaText.addEventListener('input', () => {
    const nilai = warnaText.value.trim();
    if (/^#[0-9A-Fa-f]{6}$/.test(nilai)) {
      warnaPicker.value = nilai;
      tampilkanWarna(nilai);
    }
  });

  gambarInput.addEventListener('change', () => {
    if (gambarInput.files && gambarInput.files[0]) {
      const url = URL.createObjectURL(gambarInput.files[0]);
      previewSwatch.style.backgroundImage = `url('${url}')`;
      previewSwatch.style.backgroundSize = 'cover';
      previewSwatch.style.backgroundPosition = 'center';
    } else {
      tampilkanWarna(warnaText.value.trim() || '#7C9473');
    }
  });
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>