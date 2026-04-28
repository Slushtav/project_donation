<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav>
    <a href="/donasi/index.php" class="brand">💚 DonasiKita</a>
    <div class="nav-links">
        <a href="/donasi/index.php">Beranda</a>
        <a href="/donasi/campaigns/list.php">Campaign</a>
        <?php if (isLoggedIn()): ?>
            <a href="/donasi/history/index.php">Riwayat</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/donasi/admin/index.php">Admin</a>
            <?php endif; ?>
            <a href="/donasi/auth/logout.php">Logout (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
        <?php else: ?>
            <a href="/donasi/auth/login.php">Login</a>
            <a href="/donasi/auth/register.php">Daftar</a>
        <?php endif; ?>
    </div>
</nav>
