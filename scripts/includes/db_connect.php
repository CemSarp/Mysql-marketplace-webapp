<?php
// Database connection file
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "cemsarp";
$password = "";
$dbname = "marketplace";
$port = 3306;
$socket = "/tmp/mysql.sock";

// Establish mysqli connection
$mysqli = new mysqli($servername, $username, $password, $dbname, $port, $socket);

if ($mysqli->connect_error) {
    die("MySQL Database connection failed: " . $mysqli->connect_error);
}
?>
