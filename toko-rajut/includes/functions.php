<?php
function rupiah(int $angka): string
{
    return 'Rp' . number_format($angka, 0, ',', '.');
}

function buat_slug(string $teks): string
{
    $teks = strtolower(trim($teks));
    $teks = preg_replace('/[^a-z0-9]+/', '-', $teks);
    return trim($teks, '-');
}

function bersihkan(string $teks): string
{
    return htmlspecialchars(trim($teks), ENT_QUOTES, 'UTF-8');
}

function is_admin_login(): bool
{
    return isset($_SESSION['admin_id']);
}

function wajib_login(): void
{
    if (!is_admin_login()) {
        header('Location: login.php');
        exit;
    }
}