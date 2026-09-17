<?php
/**
 * Helper untuk keranjang belanja.
 * Keranjang disimpan di session, formatnya: $_SESSION['keranjang'][produk_id] = jumlah
 */

function keranjang_tambah(int $produkId, int $jumlah, int $stokMaksimal): void
{
    $sudahAda = $_SESSION['keranjang'][$produkId] ?? 0;
    $jumlahBaru = min($stokMaksimal, $sudahAda + $jumlah);
    if ($jumlahBaru > 0) {
        $_SESSION['keranjang'][$produkId] = $jumlahBaru;
    }
}

function keranjang_update(int $produkId, int $jumlah): void
{
    if ($jumlah <= 0) {
        unset($_SESSION['keranjang'][$produkId]);
    } else {
        $_SESSION['keranjang'][$produkId] = $jumlah;
    }
}

function keranjang_hapus(int $produkId): void
{
    unset($_SESSION['keranjang'][$produkId]);
}

function keranjang_kosongkan(): void
{
    $_SESSION['keranjang'] = [];
}

function keranjang_jumlah_item(): int
{
    return array_sum($_SESSION['keranjang'] ?? []);
}

function keranjang_isi(PDO $pdo): array
{
    $keranjang = $_SESSION['keranjang'] ?? [];
    if (empty($keranjang)) {
        return [];
    }

    $ids = array_keys($keranjang);
    $placeholder = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id IN ($placeholder)");
    $stmt->execute($ids);
    $produkList = $stmt->fetchAll();

    $isi = [];
    foreach ($produkList as $p) {
        $jumlah = (int)$keranjang[$p['id']];
        $isi[] = [
            'produk' => $p,
            'jumlah' => $jumlah,
            'subtotal' => $jumlah * (int)$p['harga'],
        ];
    }
    return $isi;
}

function keranjang_total(array $isiKeranjang): int
{
    return array_sum(array_column($isiKeranjang, 'subtotal'));
}