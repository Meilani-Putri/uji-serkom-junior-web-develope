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

/**
 * Kumpulan ikon SVG inline (mewarisi warna teks lewat currentColor).
 * Dipakai supaya tombol/tautan pakai gambar ikon, bukan tulisan.
 */
function ikon(string $nama, string $kelas = ''): string
{
    $kelasAttr = $kelas !== '' ? ' class="' . $kelas . '"' : '';
    $buka = '<svg' . $kelasAttr . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';

    $isi = match ($nama) {
        'keranjang' => '<circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2.5 3h2.2l2.1 11.2a1.8 1.8 0 0 0 1.8 1.4h8.7a1.8 1.8 0 0 0 1.8-1.4L21 7.2H6"/>',
        'keranjang-plus' => '<circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2.5 3h2.2l2.1 11.2a1.8 1.8 0 0 0 1.8 1.4h8.7a1.8 1.8 0 0 0 1.8-1.4L21 7.2H6"/><path d="M13.5 8.5h4M15.5 6.5v4"/>',
        'cari'      => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'kirim'     => '<path d="M2 6.5h11v9H2z"/><path d="M13 9.5h4l3 3v3h-7z"/><circle cx="6" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
        'tangan'    => '<path d="M12 21s-7-4.3-7-9.4A3.9 3.9 0 0 1 12 9a3.9 3.9 0 0 1 7 2.6C19 16.7 12 21 12 21Z"/>',
        'perisai'   => '<path d="M12 3l7 3v5.5c0 4.3-3 7.6-7 9.5-4-1.9-7-5.2-7-9.5V6z"/><path d="m9 12 2 2 4-4"/>',
        'bintang'   => '<path d="m12 3.5 2.6 5.4 5.9.8-4.3 4.1 1 5.9-5.2-2.8-5.2 2.8 1-5.9L3.5 9.7l5.9-.8z"/>',
        'panah'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'centang'   => '<path d="m5 12.5 4.5 4.5L19 7"/>',
        'chat'      => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20.5l1.4-5.1A8 8 0 1 1 21 12Z"/>',
        'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'sampah'    => '<path d="M4 7h16M9.5 7V5.2A1.2 1.2 0 0 1 10.7 4h2.6a1.2 1.2 0 0 1 1.2 1.2V7M6.5 7l.9 12a1.6 1.6 0 0 0 1.6 1.5h6a1.6 1.6 0 0 0 1.6-1.5l.9-12"/>',
        'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="3.6"/><circle cx="17" cy="7" r="1"/>',
        'whatsapp'  => '<path d="M21 11.8a8.6 8.6 0 0 1-12.6 7.6L3.5 20.5l1.2-4.7A8.6 8.6 0 1 1 21 11.8Z"/><path d="M9 9.5c.4 2.6 2.4 4.6 5 5l1-1.4 1.7.8"/>',
        default     => '',
    };

    return $buka . $isi . '</svg>';
}