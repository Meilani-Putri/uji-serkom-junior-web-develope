</main>

<?php
// CTA "Konsultasi custom" disembunyikan di halaman yang memang sudah berisi
// ajakan/form konsultasi itu sendiri (Kontak), supaya tidak terasa diulang-ulang.
$sembunyikanCta = in_array($halaman_aktif, ['kontak'], true);
?>
<?php if (!$sembunyikanCta): ?>
<section class="footer-cta" data-aos="fade-up">
  <div class="footer-cta__inner">
    <div>
      <h2>Ingin rajutan dengan warna &amp; ukuranmu sendiri?</h2>
      <p>Ceritakan idemu, kami bantu wujudkan jadi karya rajut yang dibuat khusus untukmu.</p>
    </div>
    <div class="footer-cta__aksi">
      <a href="/toko-rajut/kontak.php" class="btn btn--solid">Konsultasi custom</a>
      <a href="/toko-rajut/produk.php" class="btn btn--ghost">Lihat katalog</a>
    </div>
  </div>
</section>
<?php endif; ?>

<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__kolom site-footer__kolom--brand">
      <p class="site-footer__brand">Amoura Atelier</p>
      <p>Setiap helai dirajut perlahan dari benang pilihan, menghadirkan karya hangat yang dibuat dengan hati dan penuh perhatian.</p>
      <div class="site-footer__sosial">
        <a href="https://wa.me/6285607153907" target="_blank" rel="noopener" aria-label="WhatsApp"><?= ikon('whatsapp') ?></a>
        <a href="/toko-rajut/kontak.php" aria-label="Kirim pesan"><?= ikon('chat') ?></a>
      </div>
    </div>
    <div class="site-footer__kolom">
      <p class="site-footer__label">Belanja</p>
      <a href="/toko-rajut/produk.php">Katalog produk</a>
      <a href="/toko-rajut/keranjang.php">Keranjang</a>
      <a href="/toko-rajut/checkout.php">Checkout</a>
    </div>
    <div class="site-footer__kolom">
      <p class="site-footer__label">Perusahaan</p>
      <a href="/toko-rajut/tentang.php">Tentang kami</a>
      <a href="/toko-rajut/kontak.php">Kontak</a>
      <a href="/profil-pribadi/index.html">Profil Developer</a>
    </div>
    <div class="site-footer__kolom">
      <p class="site-footer__label">Bantuan</p>
      <p class="site-footer__kontak">meilani1856@gmail.com</p>
      <p class="site-footer__kontak">085607153907</p>
      <p class="site-footer__kontak">Senin–Jumat, 09.00–17.00</p>
    </div>
  </div>
  <div class="site-footer__bawah">
    <p>© <?= date('Y') ?> Amoura Atelier. Proyek uji kompetensi Junior Web Developer.</p>
    <p class="site-footer__bayar">Pembayaran: Transfer Bank · COD</p>
  </div>
</footer>
<!-- Library animasi scroll: AOS (Animate On Scroll) -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="/toko-rajut/assets/js/script.js"></script>
</body>
</html>