<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/keranjang.php';

$page_title = 'Checkout';
$halaman_aktif = 'keranjang';

$isiKeranjang = keranjang_isi($pdo);
$total = keranjang_total($isiKeranjang);

if (empty($isiKeranjang)) {
    header('Location: keranjang.php');
    exit;
}

$flash = $_SESSION['checkout_flash'] ?? null;
unset($_SESSION['checkout_flash']);

$errors = $flash['errors'] ?? [];
$input = $flash['input'] ?? [
    'nama' => '',
    'email' => '',
    'telepon' => '',
    'alamat' => '',
    'metode_bayar' => 'transfer',
];

include __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-top:6vh;">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="index.php">Beranda</a> <span>/</span>
    <a href="keranjang.php">Keranjang</a> <span>/</span> <strong>Checkout</strong>
  </nav>

  <div class="section__head">
    <h2>Checkout</h2>
    <p>Lengkapi data pengiriman untuk menyelesaikan pesanan.</p>
  </div>

  <ol class="langkah-checkout">
    <li class="selesai"><span>1</span> Keranjang</li>
    <li class="aktif"><span>2</span> Data pengiriman</li>
    <li><span>3</span> Selesai</li>
  </ol>

  <div id="jsCheckoutAlert" class="alert alert--gagal" style="display: none;"></div>

  <?php if (!empty($errors)): ?>
    <div class="alert alert--gagal">
      <?= implode('<br>', array_map('bersihkan', $errors)) ?>
    </div>
  <?php endif; ?>

  <div class="checkout-grid" data-aos="fade-up">
    <form class="form" id="formCheckout" method="post" action="proses_checkout.php" onsubmit="return validasiCheckout(event)">
      <label>Nama penerima
        <input type="text" id="nama" name="nama" value="<?= bersihkan($input['nama']) ?>" required>
      </label>
      <label>Email
        <input type="email" id="email" name="email" value="<?= bersihkan($input['email']) ?>" required>
      </label>
      <label>No. WhatsApp
        <input type="tel" id="telepon" name="telepon" value="<?= bersihkan($input['telepon']) ?>" pattern="[0-9+\-\s]{8,15}" required>
      </label>
      <label>Alamat lengkap
        <input type="text" id="alamat_dummy" style="display:none;" />
        <textarea id="alamat" name="alamat" rows="4" required><?= bersihkan($input['alamat']) ?></textarea>
      </label>
      <label>Metode pembayaran
        <select name="metode_bayar">
          <option value="transfer" <?= $input['metode_bayar'] === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
          <option value="cod" <?= $input['metode_bayar'] === 'cod' ? 'selected' : '' ?>>Bayar di Tempat (COD)</option>
        </select>
      </label>
      <button type="submit" class="btn btn--solid btn--ikon">Buat Pesanan <?= ikon('panah') ?></button>
    </form>

    <div id="modalKonfirmasi" class="modal-overlay" style="display:none;">
      <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalKonfirmasiJudul">
        <h3 id="modalKonfirmasiJudul">Konfirmasi Pesanan</h3>
        <p>Apakah kamu yakin ingin membuat pesanan ini?</p>
        <div class="modal-box__aksi">
          <button type="button" class="btn btn--ghost" id="btnBatalPesanan">Batal</button>
          <button type="button" class="btn btn--solid" id="btnYakinPesanan">Ya, Buat Pesanan</button>
        </div>
      </div>
    </div>

    <div class="checkout-ringkasan">
      <h3>Ringkasan Pesanan</h3>
      <ul>
        <?php foreach ($isiKeranjang as $item): ?>
          <li>
            <span><?= bersihkan($item['produk']['nama']) ?> &times; <?= (int)$item['jumlah'] ?></span>
            <span><?= rupiah((int)$item['subtotal']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <p class="checkout-total">Total <strong><?= rupiah((int)$total) ?></strong></p>
      <ul class="checkout-jaminan">
        <li><?= ikon('perisai') ?> Data kamu hanya dipakai untuk pengiriman</li>
        <li><?= ikon('kirim') ?> Dikemas rapi sebelum dikirim</li>
      </ul>
    </div>
  </div>
</section>

<script>
function validasiCheckout(event) {
  const nama = document.getElementById('nama').value.trim();
  const email = document.getElementById('email').value.trim();
  const telepon = document.getElementById('telepon').value.trim();
  const alamat = document.getElementById('alamat').value.trim();
  const errBox = document.getElementById('jsCheckoutAlert');

  let errors = [];

  if (nama.length < 3) errors.push("Nama penerima minimal 3 karakter.");
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) errors.push("Format email tidak valid.");

  const phoneRegex = /^[0-9+\-\s]{8,15}$/;
  if (!phoneRegex.test(telepon)) errors.push("Nomor WhatsApp harus berupa angka (8–15 karakter).");

  if (alamat.length < 10) errors.push("Alamat lengkap minimal 10 karakter.");

  if (errors.length > 0) {
    event.preventDefault();
    errBox.innerHTML = errors.join('<br>');
    errBox.style.display = 'block';
    window.scrollTo(0, 0);
    return false;
  }

  errBox.style.display = 'none';
  event.preventDefault();
  document.getElementById('modalKonfirmasi').style.display = 'flex';
  return false;
}

document.getElementById('btnBatalPesanan').addEventListener('click', function () {
  document.getElementById('modalKonfirmasi').style.display = 'none';
});

document.getElementById('btnYakinPesanan').addEventListener('click', function () {
  document.getElementById('modalKonfirmasi').style.display = 'none';
  document.getElementById('formCheckout').submit();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>