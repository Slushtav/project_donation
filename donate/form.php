<?php
session_start();
require_once '../config/db.php';
requireLogin();

$campaign_id = (int)($_GET['campaign_id'] ?? 0);
$type = $_GET['type'] ?? 'uang';

$campaign = $conn->query("SELECT * FROM campaigns WHERE id=$campaign_id")->fetch_assoc();
if (!$campaign || $campaign['status'] !== 'aktif') {
    die("Campaign tidak tersedia.");
}

if (!in_array($type, ['uang','barang'])) $type = 'uang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Donasi - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <div class="form-card" style="max-width:600px">
        <h2><?= $type==='uang'?'💰':'📦' ?> Donasi <?= ucfirst($type) ?></h2>
        <p style="margin-bottom:20px;color:#666">Campaign: <strong><?= htmlspecialchars($campaign['title']) ?></strong></p>

        <form method="POST" action="submit.php">
            <input type="hidden" name="campaign_id" value="<?= $campaign_id ?>">
            <input type="hidden" name="type" value="<?= $type ?>">

            <?php if ($type === 'uang'): ?>
            <div class="form-group">
                <label>Jumlah Donasi (Rp)</label>
                <input type="number" name="amount" required min="10000" step="1000" placeholder="Contoh: 100000">
                <small style="color:#888">Minimal Rp 10.000</small>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="item_name" required placeholder="Contoh: Beras">
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="item_qty" required min="1" placeholder="Contoh: 10">
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <input type="text" name="item_unit" required placeholder="Contoh: kg, pcs, box">
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Catatan (Opsional)</label>
                <textarea name="note" placeholder="Pesan atau doa untuk penerima donasi..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">Submit Donasi</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
