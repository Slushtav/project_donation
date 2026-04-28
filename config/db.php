<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'donasi_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8");

function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/donasi/auth/login.php');
    }
}
