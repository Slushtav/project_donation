<?php
session_start();
require_once '../config/db.php';
requireLogin();
if ($_SESSION['role'] !== 'admin') redirect('/donasi/index.php');

$error = $success = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']);
    $desc    = trim($_POST['description']);
    $target  = (float)$_POST['target_amount'];
    $type    = 'keduanya';
    $status  = $_POST['status'];
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $image   = trim($_POST['existing_image'] ?? '');

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Gambar campaign gagal diupload.';
        } else {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                $error = 'Format gambar harus JPG, PNG, GIF, atau WebP.';
            } else {
                $upload_dir = __DIR__ . '/../assets/images';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0775, true);
                }

                $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '-', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
                $image = 'campaign-' . time() . '-' . $safe_name . '.' . $ext;

                if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_dir . '/' . $image)) {
                    $error = 'Gambar campaign gagal disimpan.';
                }
            }
        }
    }

    if (!$title) {
        $error = 'Judul wajib diisi.';
    } elseif (!$error && $edit_id) {
        $stmt = $conn->prepare("UPDATE campaigns SET title=?,description=?,image=?,target_amount=?,type=?,status=? WHERE id=?");
        $stmt->bind_param("sssdssi", $title, $desc, $image, $target, $type, $status, $edit_id);
        $stmt->execute();
        $success = 'Campaign berhasil diperbarui.';
    } elseif (!$error) {
        $stmt = $conn->prepare("INSERT INTO campaigns (title,description,image,target_amount,type,status) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("sssdss", $title, $desc, $image, $target, $type, $status);
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
            <form method="POST" enctype="multipart/form-data">
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
                    <label>Target Campaign (Rp)</label>
                    <input type="number" name="target_amount" value="0" min="0" step="1000" required>
                </div>
                <div class="form-group">
                    <label>Gambar Campaign</label>
                    <input type="file" name="image_file" accept="image/png,image/jpeg,image/gif,image/webp">
                    <input type="hidden" name="existing_image" value="">
                    <small style="color:#888">Upload JPG, PNG, GIF, atau WebP. Semua campaign otomatis menerima uang & barang.</small>
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
                    <tr><th>Gambar</th><th>Judul</th><th>Target</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while ($c = $campaigns->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="/donasi/assets/images/<?= htmlspecialchars($c['image'] ?? 'default.jpg') ?>"
                                 onerror="this.src='https://placehold.co/96x60/2563eb/white?text=Campaign'"
                                 class="table-thumb" alt="">
                        </td>
                        <td><?= htmlspecialchars($c['title']) ?></td>
                        <td>Rp <?= number_format($c['target_amount'], 0, ',', '.') ?></td>
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
