document.querySelectorAll('.admin-actions .hapus').forEach((link) => {
  link.addEventListener('click', (e) => {
    const yakin = confirm('Hapus produk ini? Tindakan tidak bisa dibatalkan.');
    if (!yakin) e.preventDefault();
  });
});

// Notifikasi alert otomatis hilang setelah beberapa detik
const notifAlert = document.getElementById('notifAlert');
if (notifAlert) {
  setTimeout(() => {
    notifAlert.style.transition = 'opacity .5s ease';
    notifAlert.style.opacity = '0';
    setTimeout(() => notifAlert.remove(), 500);
  }, 3500);
}