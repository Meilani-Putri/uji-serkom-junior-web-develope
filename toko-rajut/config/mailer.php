<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Kirim email konfirmasi pesan ke pembeli.
 */
function kirim_email_konfirmasi(string $emailTujuan, string $namaPembeli, string $isiPesan): bool
{
    $mail = new PHPMailer(true);

    try {
        // --- TAMPILKAN ERROR JIKA GAGAL ---
        $mail->SMTPDebug = 2; 

        $mail->isSMTP();
        
        // --- 1. PAKSA PENGGUNAAN IPv4 AGAR TIDAK HANG DI RAILWAY ---
        $mail->Host       = gethostbyname('smtp.gmail.com'); 
        
        $mail->SMTPAuth   = true;
        
        // --- 2. BACA VARIABEL DARI RAILWAY ---
        $smtpEmail = getenv('SMTP_EMAIL') ?: $_ENV['SMTP_EMAIL'] ?? '';
        $smtpPass  = getenv('SMTP_APP_PASSWORD') ?: $_ENV['SMTP_APP_PASSWORD'] ?? '';
        
        $mail->Username   = $smtpEmail;
        $mail->Password   = $smtpPass;
        
        // --- 3. GANTI JALUR KE PORT 465 (SMTPS) ---
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465; 
        
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($smtpEmail, 'Amoura Atelier');
        $mail->addAddress($emailTujuan, $namaPembeli);

        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation';
        $mail->Body    = "
            <div style='font-family:Arial,sans-serif; max-width:520px; margin:0 auto;'>
                <h2 style='color:#4B2E42;'>Terima kasih, {$namaPembeli}!</h2>
                <p>Pesan kamu sudah kami terima:</p>
                <blockquote style='background:#F7F1E4; padding:12px 16px; border-left:4px solid #D9A441; color:#2A2420;'>
                    " . nl2br(htmlspecialchars($isiPesan)) . "
                </blockquote>
                <p><strong>Status pesanan:</strong>
                    <span style='background:#7C9473; color:#fff; padding:4px 10px; border-radius:99px; font-size:13px;'>Diterima</span>
                </p>
                <p>Tim kami akan menghubungi kamu lewat email ini atau WhatsApp dalam 1x24 jam.</p>
                <p style='color:#6B6155; font-size:13px;'>— Amoura Atelier, rajutan tangan dari Madiun</p>
            </div>
        ";
        $mail->AltBody = "Terima kasih {$namaPembeli}! Pesan kamu sudah diterima. Status: Diterima. Kami akan menghubungi kamu segera.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // --- MUNCULKAN PESAN ERROR DI LAYAR ---
        echo "<h3>Gagal kirim email:</h3>";
        echo "<pre>" . $mail->ErrorInfo . "</pre>";
        die(); // Hentikan eksekusi agar tidak redirect ke halaman sukses
    }
}