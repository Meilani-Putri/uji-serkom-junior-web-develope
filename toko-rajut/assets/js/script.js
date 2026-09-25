/* ===== Konfirmasi hapus di panel admin ===== */
document.querySelectorAll('.admin-actions .hapus').forEach((link) => {
  link.addEventListener('click', (e) => {
    const yakin = confirm('Hapus produk ini? Tindakan tidak bisa dibatalkan.');
    if (!yakin) e.preventDefault();
  });
});

/* ===== Sistem toast notifikasi (melayang, hilang otomatis) ===== */
const IKON_TOAST = {
  sukses: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7"/></svg>',
  gagal: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>',
};

function ambilToastStack() {
  let stack = document.getElementById('toastStack');
  if (!stack) {
    stack = document.createElement('div');
    stack.id = 'toastStack';
    document.body.appendChild(stack);
  }
  return stack;
}

function tampilkanToast(pesanHtml, tipe = 'sukses', durasi = 4000) {
  if (!pesanHtml) return;
  const stack = ambilToastStack();

  const toast = document.createElement('div');
  toast.className = `toast toast--${tipe === 'gagal' ? 'gagal' : 'sukses'}`;
  toast.innerHTML = `
    <span class="toast__ikon">${IKON_TOAST[tipe === 'gagal' ? 'gagal' : 'sukses']}</span>
    <span class="toast__teks">${pesanHtml}</span>
    <button type="button" class="toast__tutup" aria-label="Tutup notifikasi">&times;</button>
  `;
  stack.appendChild(toast);

  requestAnimationFrame(() => toast.classList.add('toast--tampil'));

  let sudahDitutup = false;
  const tutupToast = () => {
    if (sudahDitutup) return;
    sudahDitutup = true;
    toast.classList.remove('toast--tampil');
    toast.classList.add('toast--hilang');
    setTimeout(() => toast.remove(), 260);
  };

  const timer = setTimeout(tutupToast, durasi);
  toast.querySelector('.toast__tutup').addEventListener('click', () => {
    clearTimeout(timer);
    tutupToast();
  });
}

/* Pesan yang dirender PHP langsung (setelah redirect: sukses simpan/hapus,
   status pesanan diperbarui, error validasi server, dst) otomatis diubah
   jadi toast, tanpa perlu mengubah tiap halaman satu per satu. */
document.querySelectorAll('.alert').forEach((el) => {
  const teks = el.innerHTML.trim();
  if (!teks) return;
  const tipe = el.classList.contains('alert--gagal') ? 'gagal' : 'sukses';
  tampilkanToast(teks, tipe);
  el.remove();
});

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

/* ===== Menu admin (topbar) versi mobile ===== */
const adminNavToggle = document.getElementById('adminNavToggle');
const adminBarMeta = document.querySelector('.admin-bar__meta');
if (adminNavToggle && adminBarMeta) {
  adminNavToggle.addEventListener('click', () => {
    const terbuka = adminBarMeta.classList.toggle('terbuka');
    adminNavToggle.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
  });
}

/* ===== Form ubah status pesanan: langsung submit saat dropdown diubah ===== */
document.querySelectorAll('.status-ubah').forEach((kontainer) => {
  const select = kontainer.querySelector('select');
  const tombol = kontainer.querySelector('button');
  if (select && tombol) {
    select.addEventListener('change', () => {
      tombol.classList.add('status-ubah__siap');
    });
  }
});