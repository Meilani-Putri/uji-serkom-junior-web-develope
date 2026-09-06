<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Kalau akses halaman utama (/), arahkan ke web profil
if ($uri === '/' || $uri === '') {
    header('Location: /profil-pribadi/index.html');
    exit;
}

// Kalau file/folder yang diminta memang ada di disk, biarkan PHP built-in server
// menanganinya sendiri secara normal (baik file statis maupun file .php)
$file = __DIR__ . $uri;
if ($uri !== '/' && (is_file($file) || is_dir($file))) {
    return false;
}

// Kalau tidak ketemu sama sekali, baru tampilkan 404
http_response_code(404);
echo "404 - Halaman tidak ditemukan.";