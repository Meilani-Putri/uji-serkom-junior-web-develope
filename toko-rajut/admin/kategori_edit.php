<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$page_title = 'Ubah Kategori';
$halaman_admin = 'kategori';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
$stmt->execute([$id]);
$kategori = $stmt->fetch();

if (!$kategori) {
    header('Location: kategori.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');

    if ($nama === '' || mb_strlen($nama) < 3) {
        $error = 'Nama kategori minimal 3 karakter.';
        $kategori['nama'] = $nama;
    } else {
        $stmt = $pdo->prepare("UPDATE kategori SET nama = ? WHERE id = ?");
        $stmt->execute([$nama, $id]);
        header('Location: kategori.php?status=tersimpan');
        exit;
    }
}

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="section__head">
  <h2>Ubah kategori</h2>
  <p>Perbarui nama kategori <?= bersihkan($kategori['nama']) ?>.</p>
</div>

<div id="jsErrorAlert" class="alert alert--gagal" style="display: none;"></div>
<?php if ($error): ?><div class="alert alert--gagal"><?= bersihkan($error) ?></div><?php endif; ?>

<form class="form" id="formEditKategori" method="post" action="kategori_edit.php?id=<?= (int)$id ?>" onsubmit="return validasiKategori(event)">
  <label>Nama kategori
    <input type="text" id="nama" name="nama" value="<?= bersihkan($kategori['nama']) ?>" minlength="3" required>
  </label>
  <label>Slug
    <input type="text" value="<?= bersihkan($kategori['slug']) ?>" disabled>
  </label>
  <button type="submit" class="btn btn--solid">Simpan perubahan</button>
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