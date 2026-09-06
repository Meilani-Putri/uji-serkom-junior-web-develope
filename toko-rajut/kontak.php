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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = ['tipe' => 'gagal', 'teks' => 'Format email tidak valid.'];
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
  <div class="section__head">
    <h2>Hubungi kami</h2>
    <p>Punya pertanyaan atau ingin membuat pesanan khusus? Kami dengan senang hati akan membantu mewujudkan karya yang sesuai dengan keinginanmu</p>
  </div>

  <div class="kontak-grid">
    <div class="kontak-info">
     <p><span>Email</span>meilani1856@gmail.com</p>
     <p><span>WhatsApp</span><a href="https://wa.me/085607153907" target="_blank" rel="noopener">085607153907</a></p>
     <p><span>Jam layanan</span>Senin–jumat, 09.00–10.00</p>
    </div>

    <div>
      <?php if ($status): ?>
        <div class="alert alert--<?= $status['tipe'] ?>" id="notifAlert"><?= bersihkan($status['teks']) ?></div>
      <?php endif; ?>

      <form class="form" method="post" action="kontak.php">
        <label>Nama<input type="text" name="nama" value="<?= bersihkan($_POST['nama'] ?? '') ?>" required></label>
        <label>Email<input type="email" name="email" value="<?= bersihkan($_POST['email'] ?? '') ?>" required></label>
        <label>Pesan<textarea name="pesan" rows="5" required><?= bersihkan($_POST['pesan'] ?? '') ?></textarea></label>
        <button type="submit" class="btn btn--solid">Kirim pesan</button>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>