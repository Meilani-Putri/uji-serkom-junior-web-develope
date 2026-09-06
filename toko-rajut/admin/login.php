<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$jumlahAdmin = $pdo->query("SELECT COUNT(*) AS total FROM admin")->fetch()['total'];
if ($jumlahAdmin == 0) {
    $hashDefault = password_hash('rajut123', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO admin (username, password_hash) VALUES ('admin', ?)")->execute([$hashDefault]);
}

if (is_admin_login()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk Admin — Amoura Atelier</title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/toko-rajut/assets/css/style.css">
</head>
<body class="admin-body" style="background:var(--admin-ink);">  <div class="login-box">
    <h1>Login Admin</h1>
    <?php if ($error): ?><div class="alert alert--gagal"><?= bersihkan($error) ?></div><?php endif; ?>
    <form class="form" method="post" action="login.php">
      <label>Username<input type="text" name="username" required autofocus></label>
      <label>Password<input type="password" name="password" required></label>
      <button type="submit" class="btn btn--solid">Masuk</button>
    </form>
    <p style="margin-top:18px;"><a href="/toko-rajut/index.php">← Kembali ke situs</a></p>
  </div>
</body>
</html>