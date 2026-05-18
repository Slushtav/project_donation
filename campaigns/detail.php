<?php
session_start();
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$campaign = $conn->query("SELECT * FROM campaigns WHERE id=$id")->fetch_assoc();
if (!$campaign) { echo "Campaign tidak ditemukan."; exit; }

$pct = $campaign['target_amount'] > 0
    ? min(100, round($campaign['collected_amount'] / $campaign['target_amount'] * 100))
    : 0;

$recent = $conn->query("
    SELECT d.*, u.name as donor_name 
    FROM donations d JOIN users u ON d.user_id=u.id 
    WHERE d.campaign_id=$id AND d.status != 'ditolak'
    ORDER BY d.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($campaign['title']) ?> - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <div class="layout-detail">
        <div>
            <img src="/donasi/assets/images/<?= htmlspecialchars($campaign['image'] ?? 'default.jpg') ?>"
                 onerror="this.src='https://placehold.co/700x300/1a73e8/white?text=Campaign'"
                 class="detail-cover" alt="">
            <h1 style="font-size:1.5rem;margin-bottom:10px"><?= htmlspecialchars($campaign['title']) ?></h1>
            <p style="color:#555;line-height:1.7"><?= nl2br(htmlspecialchars($campaign['description'])) ?></p>

            <?php if ($recent->num_rows > 0): ?>
            <h3 style="margin-top:24px;margin-bottom:12px">Donatur Terbaru</h3>
            <?php while ($d = $recent->fetch_assoc()): ?>
            <div class="donor-row">
                <span>👤 <?= htmlspecialchars($d['donor_name']) ?></span>
                <span style="color:#1a73e8;font-weight:600">
                    <?= $d['type']==='uang' ? 'Rp '.number_format($d['amount'],0,',','.') : '📦 '.$d['item_name'] ?>
                </span>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="card sticky-card">
            <h3 style="margin-bottom:12px">Informasi Campaign</h3>
            <span class="badge badge-<?= $campaign['status'] ?>"><?= ucfirst($campaign['status']) ?></span>
            <br><br>

            <?php if ($campaign['target_amount'] > 0): ?>
            <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div>
            <p style="font-size:0.85rem;margin:6px 0">
                <strong>Rp <?= number_format($campaign['collected_amount'],0,',','.') ?></strong>
                dari Rp <?= number_format($campaign['target_amount'],0,',','.') ?> (<?= $pct ?>%)
            </p>
            <?php endif; ?>

            <div class="pixel-panel donation-prompt">
                <span class="pixel-heart" aria-hidden="true"></span>
                <div>
                    <strong>Pilih jenis donasi</strong>
                    <p>Semua campaign bisa dibantu dengan uang atau barang.</p>
                </div>
            </div>

            <?php if ($campaign['status'] === 'aktif'): ?>
                <?php if (isLoggedIn()): ?>
                    <div class="donation-choice-grid">
                        <a href="/donasi/donate/form.php?campaign_id=<?= $id ?>&type=uang" class="type-card donation-choice money">
                            <span class="icon">💰</span>
                            <h4>Donasi Uang</h4>
                            <p>Bantu cepat lewat nominal terbaikmu.</p>
                        </a>
                        <a href="/donasi/donate/form.php?campaign_id=<?= $id ?>&type=barang" class="type-card donation-choice goods">
                            <span class="icon">📦</span>
                            <h4>Donasi Barang</h4>
                            <p>Kirim barang layak untuk kebutuhan campaign.</p>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/donasi/auth/login.php" class="btn btn-primary" style="width:100%;text-align:center">Login untuk Pilih Donasi</a>
                <?php endif; ?>
            <?php else: ?>
                <p class="alert alert-info">Campaign ini sudah ditutup.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
