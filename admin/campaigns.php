<?php
session_start();
require_once '../config/db.php';
requireLogin();
if ($_SESSION['role'] !== 'admin') redirect('/donasi/index.php');

$error = $success = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title']);
    $desc   = trim($_POST['description']);
    $target = (float)$_POST['target_amount'];
    $type   = $_POST['type'];
    $status = $_POST['status'];
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if (!$title) {
        $error = 'Judul wajib diisi.';
    } elseif ($edit_id) {
        $stmt = $conn->prepare("UPDATE campaigns SET title=?,description=?,target_amount=?,type=?,status=? WHERE id=?");
        $stmt->bind_param("ssdssi", $title, $desc, $target, $type, $status, $edit_id);
        $stmt->execute();
        $success = 'Campaign berhasil diperbarui.';
    } else {
        $stmt = $conn->prepare("INSERT INTO campaigns (title,description,target_amount,type,status) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssdss", $title, $desc, $target, $type, $status);
        $stmt->execute();
        $success = 'Campaign berhasil ditambahkan.';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $del = (int)$_GET['delete'];
    $conn->query("DELETE FROM campaigns WHERE id=$del");
    redirect('/donasi/admin/campaigns.php');
}

$campaigns = $conn->query("SELECT * FROM campaigns ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Campaign - Admin</title>
    <link rel="stylesheet" href="/donasi/assets/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2 class="section-title">📋 Kelola Campaign</h2>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <div class="admin-grid">
        <!-- Form -->
        <div class="card sticky-card">
            <h3 style="margin-bottom:16px">Tambah Campaign</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" value="0">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description"></textarea>
                </div>
                <div class="form-group">
                    <label>Target (Rp, 0 = tanpa target)</label>
                    <input type="number" name="target_amount" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Jenis Donasi</label>
                    <select name="type">
                        <option value="keduanya">Uang & Barang</option>
                        <option value="uang">Uang saja</option>
                        <option value="barang">Barang saja</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="aktif">Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditutup">Ditutup</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Simpan</button>
            </form>
        </div>

        <!-- List -->
        <div>
            <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Judul</th><th>Jenis</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while ($c = $campaigns->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['title']) ?></td>
                        <td><?= ucfirst($c['type']) ?></td>
                        <td><span class="badge badge-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                        <td>
                            <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus campaign ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
