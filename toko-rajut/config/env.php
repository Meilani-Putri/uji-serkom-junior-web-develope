<?php
function muat_env(string $path): void
{
    if (!file_exists($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $baris) {
        $baris = trim($baris);
        if ($baris === '' || str_starts_with($baris, '#')) {
            continue;
        }
        [$kunci, $nilai] = array_map('trim', explode('=', $baris, 2));
        $_ENV[$kunci] = $nilai;
    }
}

muat_env(__DIR__ . '/../.env');

function is_development(): bool
{
    return ($_ENV['APP_ENV'] ?? 'production') === 'development';
}