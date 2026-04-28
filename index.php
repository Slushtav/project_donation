<?php
session_start();
require_once 'config/db.php';

$campaigns = $conn->query("SELECT * FROM campaigns WHERE status='aktif' LIMIT 6");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonasiKita - Berbagi dengan Ikhlas</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <div class="hero">
        <h1>💚 Bersama Kita Bisa Berbuat Lebih</h1>
        <p>Donasikan uang atau barang untuk membantu sesama yang membutuhkan.</p>
        <a href="/donasi/campaigns/list.php" class="btn btn-outline" style="border-color:#fff;color:#fff;">Lihat Campaign</a>
    </div>

    <h2 class="section-title">Campaign Aktif</h2>
    <div class="grid-3">
        <?php while ($c = $campaigns->fetch_assoc()): 
            $pct = $c['target_amount'] > 0 ? min(100, round($c['collected_amount'] / $c['target_amount'] * 100)) : 0;
        ?>
        <div class="card">
            <img src="/donasi/assets/images/<?= htmlspecialchars($c['image'] ?? 'default.jpg') ?>" 
                 onerror="this.src='https://placehold.co/400x180/1a73e8/white?text=Campaign'"
                 alt="<?= htmlspecialchars($c['title']) ?>">
            <div class="card-body">
                <span class="badge badge-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span>
                <h3><?= htmlspecialchars($c['title']) ?></h3>
                <p><?= htmlspecialchars(substr($c['description'], 0, 80)) ?>...</p>
                <?php if ($c['target_amount'] > 0): ?>
                <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div>
                <small>Rp <?= number_format($c['collected_amount'], 0, ',', '.') ?> dari Rp <?= number_format($c['target_amount'], 0, ',', '.') ?></small>
                <?php endif; ?>
                <br><br>
                <a href="/donasi/campaigns/detail.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Donasi Sekarang</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
