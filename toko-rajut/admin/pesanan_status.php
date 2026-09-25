<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
wajib_login();

$id     = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$kembali = $_POST['kembali'] ?? 'pesanan.php';

// Amankan redirect: hanya boleh kembali ke halaman di dalam folder admin ini
if (!preg_match('/^[a-z_]+\.php(\?[a-zA-Z0-9=&%._\-]*)?$/', $kembali)) {
    $kembali = 'pesanan.php';
}

$statusValid = array_keys(daftar_status_pesanan());

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0 || !in_array($status, $statusValid, true)) {
    $_SESSION['flash_pesanan'] = ['tipe' => 'gagal', 'teks' => 'Permintaan tidak valid.'];
    header('Location: ' . $kembali);
    exit;
}

$stmt = $pdo->prepare('UPDATE pesanan SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['flash_pesanan'] = ['tipe' => 'sukses', 'teks' => 'Status pesanan berhasil diperbarui.'];
} else {
    $_SESSION['flash_pesanan'] = ['tipe' => 'gagal', 'teks' => 'Pesanan tidak ditemukan.'];
}

header('Location: ' . $kembali);
exit;