<?php
// includes/mongo_connect.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mongoError = "";
try {
    // Try connecting with a short timeout
    $manager = new MongoDB\Driver\Manager(
        "mongodb://localhost:27017",
        ["serverSelectionTimeoutMS" => 2000]
    );
    $ticketsCollection = "marketplace.tickets";
} catch (MongoDB\Driver\Exception\Exception $e) {
    // Connection failed
    $manager = null;
    $mongoError = "Cannot connect to MongoDB: " . $e->getMessage();
}
