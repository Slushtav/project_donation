<?php
session_start();
require_once '../config/db.php';

if (isLoggedIn()) redirect('/donasi/index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        redirect('/donasi/index.php');
    } else {
        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="form-card">
    <h2>🔐 Login</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if (isset($_GET['msg'])): ?><div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="email@contoh.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Password kamu">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Login</button>
    </form>
    <p style="margin-top:16px;text-align:center;font-size:0.9rem">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
