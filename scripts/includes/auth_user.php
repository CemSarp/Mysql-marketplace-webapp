<?php
// User authentication check
require_once __DIR__ . "/session.php";

if (!isset($_SESSION['userid']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    $current_dir = basename(getcwd());
    if ($current_dir === 'user') {
        header("Location: login.php");
    } else {
        header("Location: user/login.php");
    }
    exit;
}
?>
