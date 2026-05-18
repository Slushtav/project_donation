<?php
session_start();
require_once '../config/db.php';

$campaigns = $conn->query("SELECT * FROM campaigns WHERE status='aktif' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2 class="section-title">Pilih Campaign</h2>

    <div class="info-panel campaign-note">
        <span class="info-icon" aria-hidden="true">🤝</span>
        <div>
            <strong>Semua campaign menerima donasi uang dan barang.</strong>
            <p>Pilih campaign yang ingin kamu bantu, lalu tentukan jenis donasinya di halaman detail.</p>
        </div>
    </div>

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
                <p><?= htmlspecialchars(substr($c['description'], 0, 90)) ?>...</p>
                <?php if ($c['target_amount'] > 0): ?>
                <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div>
                <small><?= $pct ?>% &bull; Rp <?= number_format($c['collected_amount'], 0, ',', '.') ?></small>
                <?php endif; ?>
                <br><br>
                <a href="detail.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Pilih Campaign</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
