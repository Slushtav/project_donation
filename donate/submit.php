<?php
session_start();
require_once '../config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/donasi/index.php');

$user_id     = $_SESSION['user_id'];
$campaign_id = (int)$_POST['campaign_id'];
$type        = $_POST['type'];

if (!in_array($type, ['uang', 'barang'])) {
    die("Jenis donasi tidak valid.");
}

$campaign = $conn->query("SELECT * FROM campaigns WHERE id=$campaign_id AND status='aktif'")->fetch_assoc();
if (!$campaign) die("Campaign tidak valid.");

if ($type === 'uang') {
    $amount = (float)$_POST['amount'];
    if ($amount < 10000) die("Jumlah donasi minimal Rp 10.000");
    $note = trim($_POST['note'] ?? '');

    $stmt = $conn->prepare("INSERT INTO donations (user_id, campaign_id, type, amount, note) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisds", $user_id, $campaign_id, $type, $amount, $note);
    $stmt->execute();

    // Donasi uang menambah progress bar target campaign.
    $update = $conn->prepare("UPDATE campaigns SET collected_amount = collected_amount + ? WHERE id=?");
    $update->bind_param("di", $amount, $campaign_id);
    $update->execute();

} else {
    $item_name = trim($_POST['item_name']);
    $item_qty  = (int)$_POST['item_qty'];
    $item_unit = trim($_POST['item_unit']);
    $note      = trim($_POST['note'] ?? '');

    $stmt = $conn->prepare("INSERT INTO donations (user_id, campaign_id, type, item_name, item_qty, item_unit, note) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("iississ", $user_id, $campaign_id, $type, $item_name, $item_qty, $item_unit, $note);
    $stmt->execute();
}

$donation_id = $conn->insert_id;
redirect("/donasi/donate/success.php?id=$donation_id");
