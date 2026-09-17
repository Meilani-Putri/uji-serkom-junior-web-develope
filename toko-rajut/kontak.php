<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Kontak';
$halaman_aktif = 'kontak';

$status = null;

if (isset($_GET['status']) && $_GET['status'] === 'sukses') {
    $status = ['tipe' => 'sukses', 'teks' => 'Pesan terkirim. Cek email kamu untuk konfirmasi — kami akan menghubungi dalam 1x24 jam.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $isiPesan = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $isiPesan === '') {
        $status = ['tipe' => 'gagal', 'teks' => 'Semua kolom wajib diisi.'];
    } elseif (mb_strlen($nama) < 3) {
        $status = ['tipe' => 'gagal', 'teks' => 'Nama minimal 3 karakter.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = ['tipe' => 'gagal', 'teks' => 'Format email tidak valid.'];
    } elseif (mb_strlen($isiPesan) < 10) {
        $status = ['tipe' => 'gagal', 'teks' => 'Pesan minimal 10 karakter.'];
    } else {
        require_once __DIR__ . '/config/mailer.php';

        $stmt = $pdo->prepare("INSERT INTO pesan (nama, email, isi_pesan, status) VALUES (?, ?, ?, 'diterima')");
        $stmt->execute([$nama, $email, $isiPesan]);

        kirim_email_konfirmasi($email, $nama, $isiPesan);

        header('Location: kontak.php?status=sukses');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="index.php">Beranda</a> <span>/</span> <strong>Kontak</strong>
  </nav>

  <div class="section__head">
    <h2>Hubungi kami</h2>
    <p>Punya pertanyaan atau ingin membuat pesanan khusus? Kami dengan senang hati akan membantu mewujudkan karya yang sesuai dengan keinginanmu</p>
  </div>

  <div class="kontak-grid" data-aos="fade-up">
    <div class="kontak-info">
      <div class="kontak-kartu">
        <?= ikon('chat') ?>
        <div><span>Email</span>meilani1856@gmail.com</div>
      </div>
      <div class="kontak-kartu">
        <?= ikon('whatsapp') ?>
        <div><span>WhatsApp</span><a href="https://wa.me/6285607153907" target="_blank" rel="noopener">085607153907</a></div>
      </div>
      <div class="kontak-kartu">
        <?= ikon('centang') ?>
        <div><span>Jam layanan</span>Senin–Jumat, 09.00–17.00</div>
      </div>
      <div class="kontak-kartu">
        <?= ikon('tangan') ?>
        <div><span>Studio</span>Ponorogo, Jawa Timur</div>
      </div>
    </div>

    <div>
      <?php if ($status): ?>
        <div class="alert alert--<?= $status['tipe'] ?>" id="notifAlert"><?= bersihkan($status['teks']) ?></div>
      <?php endif; ?>

      <div id="jsKontakAlert" class="alert alert--gagal" style="display: none;"></div>

      <form class="form" id="formKontak" method="post" action="kontak.php" onsubmit="return validasiKontak(event)">
        <label>Nama<input type="text" id="nama" name="nama" value="<?= bersihkan($_POST['nama'] ?? '') ?>" minlength="3" required></label>
        <label>Email<input type="email" id="email" name="email" value="<?= bersihkan($_POST['email'] ?? '') ?>" required></label>
        <label>Pesan<textarea id="pesan" name="pesan" rows="5" minlength="10" required><?= bersihkan($_POST['pesan'] ?? '') ?></textarea></label>
        <button type="submit" class="btn btn--solid">Kirim pesan</button>
      </form>
    </div>
  </div>
</section>

<script>
function validasiKontak(event) {
  const nama = document.getElementById('nama').value.trim();
  const email = document.getElementById('email').value.trim();
  const pesan = document.getElementById('pesan').value.trim();
  const errBox = document.getElementById('jsKontakAlert');

  let errors = [];

  if (nama.length < 3) errors.push("Nama minimal 3 karakter.");

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) errors.push("Format email tidak valid.");

  if (pesan.length < 10) errors.push("Pesan minimal 10 karakter.");

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

<?php include __DIR__ . '/includes/footer.php'; ?>