<?php
session_start();
require_once '../config/db.php';

if (isLoggedIn()) redirect('/donasi/index.php');

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $pass2 = $_POST['password2'];

    if (!$name || !$email || !$pass) {
        $error = 'Semua field wajib diisi.';
    } elseif ($pass !== $pass2) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();
            $success = 'Registrasi berhasil! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="form-card">
    <h2>📝 Daftar Akun</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?> <a href="login.php">Login</a></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" required placeholder="Nama kamu">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="email@contoh.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Min. 6 karakter">
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="password2" required placeholder="Ulangi password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Daftar</button>
    </form>
    <p style="margin-top:16px;text-align:center;font-size:0.9rem">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
