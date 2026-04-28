<?php
session_start();
require_once '../config/db.php';
requireLogin();
if ($_SESSION['role'] !== 'admin') redirect('/donasi/index.php');

$id     = (int)($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';
$allowed = ['dikonfirmasi','selesai','ditolak'];

if ($id && in_array($status, $allowed)) {
    $stmt = $conn->prepare("UPDATE donations SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

redirect('/donasi/admin/index.php');
