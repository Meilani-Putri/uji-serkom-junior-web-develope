<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/keranjang.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

$isiKeranjang = keranjang_isi($pdo);
if (empty($isiKeranjang)) {
    header('Location: keranjang.php');
    exit;
}

$input = [
    'nama' => trim($_POST['nama'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'telepon' => trim($_POST['telepon'] ?? ''),
    'alamat' => trim($_POST['alamat'] ?? ''),
    'metode_bayar' => $_POST['metode_bayar'] ?? 'transfer',
];

// ---- Validasi form ----
$errors = [];
if ($input['nama'] === '') {
    $errors[] = 'Nama penerima wajib diisi.';
}
if ($input['email'] === '' || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}
if ($input['telepon'] === '' || !preg_match('/^[0-9+\-\s]{8,15}$/', $input['telepon'])) {
    $errors[] = 'Nomor WhatsApp tidak valid.';
}
if ($input['alamat'] === '') {
    $errors[] = 'Alamat pengiriman wajib diisi.';
}
if (!in_array($input['metode_bayar'], ['transfer', 'cod'], true)) {
    $errors[] = 'Metode pembayaran tidak valid.';
}

if (!empty($errors)) {
    $_SESSION['checkout_flash'] = ['errors' => $errors, 'input' => $input];
    header('Location: checkout.php');
    exit;
}

// ---- Validasi ulang stok (bisa berubah sejak halaman checkout dibuka) ----
foreach ($isiKeranjang as $item) {
    if ($item['jumlah'] > (int)$item['produk']['stok']) {
        $_SESSION['checkout_flash'] = [
            'errors' => ['Stok "' . $item['produk']['nama'] . '" tidak cukup, sisa ' . $item['produk']['stok'] . ' pcs.'],
            'input' => $input,
        ];
        header('Location: keranjang.php');
        exit;
    }
}

$total = keranjang_total($isiKeranjang);
$kodePesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
$statusAwal = $input['metode_bayar'] === 'cod' ? 'diproses' : 'menunggu_pembayaran';

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO pesanan (kode_pesanan, nama_pembeli, email_pembeli, telepon_pembeli, alamat_kirim, metode_bayar, total_harga, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        RETURNING id
    ");
    $stmt->execute([
        $kodePesanan,
        $input['nama'],
        $input['email'],
        $input['telepon'],
        $input['alamat'],
        $input['metode_bayar'],
        $total,
        $statusAwal,
    ]);
    $pesananId = (int)$stmt->fetchColumn();

    $stmtItem = $pdo->prepare("
        INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, harga_saat_beli, jumlah, subtotal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtStok = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id = ? AND stok >= ?");

    foreach ($isiKeranjang as $item) {
        $stmtItem->execute([
            $pesananId,
            $item['produk']['id'],
            $item['produk']['nama'],
            $item['produk']['harga'],
            $item['jumlah'],
            $item['subtotal'],
        ]);

        $stmtStok->execute([$item['jumlah'], $item['produk']['id'], $item['jumlah']]);
        if ($stmtStok->rowCount() === 0) {
            throw new RuntimeException('Stok produk "' . $item['produk']['nama'] . '" berubah, silakan coba lagi.');
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    $_SESSION['checkout_flash'] = ['errors' => [$e->getMessage()], 'input' => $input];
    header('Location: keranjang.php');
    exit;
}

keranjang_kosongkan();
header('Location: pesanan_selesai.php?id=' . $pesananId);
exit;