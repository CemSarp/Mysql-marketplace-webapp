<?php
// Admin authentication check
require_once __DIR__ . "/session.php";

if (!isset($_SESSION['userid']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $current_dir = basename(getcwd());
    if ($current_dir === 'admin') {
        header("Location: login.php");
    } else {
        header("Location: admin/login.php");
    }
    exit;
}
?>
