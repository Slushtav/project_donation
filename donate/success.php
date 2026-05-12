<?php
session_start();
require_once '../config/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("
    SELECT d.*, c.title as campaign_title 
    FROM donations d JOIN campaigns c ON d.campaign_id=c.id 
    WHERE d.id=? AND d.user_id=?
");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$donation = $stmt->get_result()->fetch_assoc();

if (!$donation) redirect('/donasi/index.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donasi Berhasil - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <div class="form-card" style="max-width:560px;text-align:center">
        <div style="font-size:4rem;margin-bottom:16px">🎉</div>
        <h2 style="color:#34a853;margin-bottom:8px">Donasi Berhasil Dikirim!</h2>
        <p style="color:#666;margin-bottom:24px">Terima kasih atas kebaikan hatimu. Donasi kamu sedang diproses.</p>

        <!-- Status Steps -->
        <div class="steps">
            <div class="step done">
                <div class="step-circle">✓</div>
                <div class="step-label">Submit</div>
            </div>
            <div class="step active">
                <div class="step-circle">2</div>
                <div class="step-label">Diproses</div>
            </div>
            <div class="step">
                <div class="step-circle">3</div>
                <div class="step-label">Dikonfirmasi</div>
            </div>
            <div class="step">
                <div class="step-circle">4</div>
                <div class="step-label">Selesai</div>
            </div>
        </div>

        <div style="background:#f9f9f9;border-radius:8px;padding:16px;margin:20px 0;text-align:left">
            <p><strong>Campaign:</strong> <?= htmlspecialchars($donation['campaign_title']) ?></p>
            <p><strong>Jenis:</strong> <?= ucfirst($donation['type']) ?></p>
            <?php if ($donation['type'] === 'uang'): ?>
            <p><strong>Jumlah:</strong> Rp <?= number_format($donation['amount'], 0, ',', '.') ?></p>
            <?php else: ?>
            <p><strong>Barang:</strong> <?= htmlspecialchars($donation['item_name']) ?> &mdash; <?= $donation['item_qty'] ?> <?= htmlspecialchars($donation['item_unit']) ?></p>
            <?php endif; ?>
            <p><strong>Status:</strong> <span class="badge badge-diproses">Diproses</span></p>
        </div>

        <a href="/donasi/history/index.php" class="btn btn-primary">Lihat Riwayat Donasi</a>
        <a href="/donasi/campaigns/list.php" class="btn btn-outline" style="margin-left:8px">Campaign Lainnya</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
