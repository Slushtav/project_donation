<?php
session_start();
require_once '../config/db.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$donations = $conn->query("
    SELECT d.*, c.title as campaign_title 
    FROM donations d JOIN campaigns c ON d.campaign_id=c.id 
    WHERE d.user_id=$user_id 
    ORDER BY d.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Donasi - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2 class="section-title">📋 Riwayat Donasi Saya</h2>

    <?php if ($donations->num_rows === 0): ?>
        <div class="alert alert-info">Kamu belum pernah berdonasi. <a href="/donasi/campaigns/list.php">Mulai donasi sekarang!</a></div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Campaign</th>
                <th>Jenis</th>
                <th>Detail</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while ($d = $donations->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($d['campaign_title']) ?></td>
                <td><?= ucfirst($d['type']) ?></td>
                <td>
                    <?php if ($d['type'] === 'uang'): ?>
                        💰 Rp <?= number_format($d['amount'], 0, ',', '.') ?>
                    <?php else: ?>
                        📦 <?= htmlspecialchars($d['item_name']) ?> (<?= $d['item_qty'] ?> <?= htmlspecialchars($d['item_unit']) ?>)
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-<?= $d['status'] ?>"><?= ucfirst($d['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
