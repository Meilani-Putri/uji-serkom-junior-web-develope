-- Skema database Simpul Rajut

CREATE TABLE kategori (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  slug VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE produk (
  id SERIAL PRIMARY KEY,
  kategori_id INT NOT NULL REFERENCES kategori(id) ON DELETE CASCADE,
  nama VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  harga INT NOT NULL CHECK (harga >= 0),
  stok INT NOT NULL DEFAULT 0 CHECK (stok >= 0),
  warna VARCHAR(7) NOT NULL DEFAULT '#7C9473',
  gambar VARCHAR(255) DEFAULT NULL,
  deskripsi TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pesan (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(80) NOT NULL,
  email VARCHAR(120) NOT NULL,
  isi_pesan TEXT NOT NULL,
  status VARCHAR(20) DEFAULT 'diterima',
  dikirim_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admin (
  id SERIAL PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

INSERT INTO kategori (nama, slug) VALUES
('Baju Rajut', 'baju-rajut'),
('Sweater Rajut', 'sweater-rajut'),
('Tas Rajut', 'tas-rajut'),
('Mainan & Aksesoris Rajut', 'mainan-aksesoris');

INSERT INTO produk (kategori_id, nama, slug, harga, stok, warna, deskripsi) VALUES
(1, 'Kardigan Rajut Amara', 'kardigan-rajut-amara', 185000, 12, '#8A6E52', 'Kardigan rajut rajutan tangan dengan benang katun lokal, cocok untuk cuaca sejuk. Tersedia warna cokelat susu dengan kancing kayu.'),
(1, 'Rompi Rajut Nari', 'rompi-rajut-nari', 145000, 8, '#7C9473', 'Rompi rajut motif kabel klasik, dipadukan dengan kemeja atau kaos polos untuk tampilan kasual rapi.'),
(2, 'Sweater Rajut Wulan', 'sweater-rajut-wulan', 220000, 10, '#4B2E42', 'Sweater rajut tebal dengan pola chevron, dirajut dari benang wol campur agar tetap hangat namun ringan.'),
(2, 'Sweater Turtleneck Damar', 'sweater-turtleneck-damar', 235000, 6, '#2A2420', 'Sweater leher tinggi berbahan wol akrilik premium, dirajut rapat untuk perlindungan maksimal dari udara dingin.'),
(3, 'Tas Rajut Kanvas Senja', 'tas-rajut-kanvas-senja', 95000, 15, '#D9A441', 'Tas tote rajut dengan lapisan kanvas di dalamnya, cukup kuat membawa buku dan laptop tipis sehari-hari.'),
(3, 'Tas Selempang Mini Kirana', 'tas-selempang-mini-kirana', 78000, 20, '#B25D4C', 'Tas selempang rajut ukuran mini, pas untuk dompet dan ponsel, dilengkapi tali kulit sintetis yang bisa diatur panjangnya.'),
(4, 'Boneka Rajut Kelinci Momo', 'boneka-rajut-kelinci-momo', 65000, 25, '#C9A8B0', 'Boneka rajut amigurumi berbentuk kelinci, aman untuk anak karena diisi dakron food-grade dan jahitan tersembunyi.'),
(4, 'Gantungan Kunci Rajut Set', 'gantungan-kunci-rajut-set', 25000, 40, '#7C9473', 'Satu set berisi tiga gantungan kunci rajut mini dengan bentuk buah-buahan, cocok untuk suvenir atau hadiah kecil.');