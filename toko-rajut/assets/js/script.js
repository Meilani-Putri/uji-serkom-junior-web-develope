/* ===== Konfirmasi hapus di panel admin ===== */
document.querySelectorAll('.admin-actions .hapus').forEach((link) => {
  link.addEventListener('click', (e) => {
    const yakin = confirm('Hapus produk ini? Tindakan tidak bisa dibatalkan.');
    if (!yakin) e.preventDefault();
  });
});

/* ===== Notifikasi alert otomatis hilang setelah beberapa detik ===== */
const notifAlert = document.getElementById('notifAlert');
if (notifAlert) {
  setTimeout(() => {
    notifAlert.style.transition = 'opacity .5s ease';
    notifAlert.style.opacity = '0';
    setTimeout(() => notifAlert.remove(), 500);
  }, 3500);
}

/* ===== Menu mobile ===== */
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');
if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const terbuka = navLinks.classList.toggle('terbuka');
    navToggle.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
    navToggle.setAttribute('aria-label', terbuka ? 'Tutup menu' : 'Buka menu');
  });
}

/* ===== Bayangan navbar saat halaman digulir ===== */
const siteNav = document.getElementById('siteNav');
if (siteNav) {
  const cekGulir = () => siteNav.classList.toggle('is-scrolled', window.scrollY > 8);
  cekGulir();
  window.addEventListener('scroll', cekGulir, { passive: true });
}

/* ===== Umpan balik saat produk ditambahkan ke keranjang ===== */
document.querySelectorAll('.product-card__form').forEach((form) => {
  form.addEventListener('submit', () => {
    const tombol = form.querySelector('.product-card__tambah');
    if (tombol) {
      tombol.classList.add('sedang-proses');
      tombol.disabled = true;
    }
  });
});

/* ===== Inisialisasi AOS (library animasi scroll) ===== */
if (typeof AOS !== 'undefined') {
  AOS.init({
    duration: 600,
    easing: 'ease-out',
    once: true,
    offset: 60,
  });
}