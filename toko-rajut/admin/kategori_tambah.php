<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Tambah Kategori';
$halaman_admin = 'kategori';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');

    if ($nama === '' || mb_strlen($nama) < 3) {
        $error = 'Nama kategori minimal 3 karakter.';
    } else {
        $slug = buat_slug($nama);

        $cek = $pdo->prepare("SELECT id FROM kategori WHERE slug = ?");
        $cek->execute([$slug]);
        if ($cek->fetch()) {
            $slug .= '-' . substr(uniqid(), -4);
        }

        $stmt = $pdo->prepare("INSERT INTO kategori (nama, slug) VALUES (?, ?)");
        $stmt->execute([$nama, $slug]);

        header('Location: kategori.php?status=tersimpan');
        exit;
    }
}

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Tambah kategori</h2>
  <p>Buat kategori baru untuk mengelompokkan produk.</p>
</div>

<div id="jsErrorAlert" class="alert alert--gagal" style="display: none;"></div>
<?php if ($error): ?><div class="alert alert--gagal"><?= bersihkan($error) ?></div><?php endif; ?>

<form class="form" id="formTambahKategori" method="post" action="kategori_tambah.php" onsubmit="return validasiKategori(event)">
  <label>Nama kategori
    <input type="text" id="nama" name="nama" value="<?= bersihkan($_POST['nama'] ?? '') ?>" minlength="3" required>
  </label>
  <button type="submit" class="btn btn--solid">Simpan kategori</button>
</form>

<script>
function validasiKategori(event) {
  const nama = document.getElementById('nama').value.trim();
  const errBox = document.getElementById('jsErrorAlert');
  let errors = [];

  if (nama.length < 3) errors.push("Nama kategori minimal 3 karakter.");

  if (errors.length > 0) {
    event.preventDefault();
    errBox.innerHTML = errors.join('<br>');
    errBox.style.display = 'block';
    window.scrollTo(0, 0);
    return false;
  }
  return true;
}
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>