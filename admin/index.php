<?php
session_start();
require_once '../config/db.php';
requireLogin();
if ($_SESSION['role'] !== 'admin') redirect('/donasi/index.php');

$total_donations = $conn->query("SELECT COUNT(*) as c FROM donations")->fetch_assoc()['c'];
$total_uang = $conn->query("SELECT SUM(amount) as s FROM donations WHERE type='uang' AND status!='ditolak'")->fetch_assoc()['s'] ?? 0;
$total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM donations WHERE status='diproses'")->fetch_assoc()['c'];

$donations = $conn->query("
    SELECT d.*, u.name as donor_name, c.title as campaign_title 
    FROM donations d 
    JOIN users u ON d.user_id=u.id 
    JOIN campaigns c ON d.campaign_id=c.id 
    ORDER BY d.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - DonasiKita</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2 class="section-title">⚙️ Dashboard Admin</h2>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:30px">
        <?php foreach ([
            ['💰 Total Uang', 'Rp '.number_format($total_uang,0,',','.')],
            ['📦 Total Donasi', $total_donations.' donasi'],
            ['👥 Pengguna', $total_users.' user'],
            ['⏳ Menunggu', $pending.' pending'],
        ] as $s): ?>
        <div class="card" style="padding:20px;text-align:center">
            <div style="font-size:1.5rem;font-weight:700;color:#1a73e8"><?= $s[1] ?></div>
            <div style="font-size:0.85rem;color:#666;margin-top:4px"><?= $s[0] ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h3>Semua Donasi</h3>
        <a href="campaigns.php" class="btn btn-primary btn-sm">Kelola Campaign</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Donatur</th>
                <th>Campaign</th>
                <th>Jenis</th>
                <th>Detail</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($d = $donations->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($d['donor_name']) ?></td>
                <td><?= htmlspecialchars($d['campaign_title']) ?></td>
                <td><?= ucfirst($d['type']) ?></td>
                <td>
                    <?= $d['type']==='uang'
                        ? 'Rp '.number_format($d['amount'],0,',','.')
                        : htmlspecialchars($d['item_name']).' ('.$d['item_qty'].' '.htmlspecialchars($d['item_unit']).')' ?>
                </td>
                <td><span class="badge badge-<?= $d['status'] ?>"><?= ucfirst($d['status']) ?></span></td>
                <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                <td>
                    <?php if ($d['status'] === 'diproses'): ?>
                    <a href="update_status.php?id=<?= $d['id'] ?>&status=dikonfirmasi" class="btn btn-success btn-sm">Konfirmasi</a>
                    <a href="update_status.php?id=<?= $d['id'] ?>&status=ditolak" class="btn btn-danger btn-sm" onclick="return confirm('Tolak donasi ini?')">Tolak</a>
                    <?php elseif ($d['status'] === 'dikonfirmasi'): ?>
                    <a href="update_status.php?id=<?= $d['id'] ?>&status=selesai" class="btn btn-primary btn-sm">Selesai</a>
                    <?php else: ?>
                    <span style="color:#aaa;font-size:0.8rem">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
