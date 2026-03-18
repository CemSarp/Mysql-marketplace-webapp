<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST["name"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $password = trim($_POST["password"] ?? "");

    try {
        $mysqli->begin_transaction();

        // Step 1: Check if user with same name and phone already exists
        $checkStmt = $mysqli->prepare("SELECT userid FROM Users WHERE name = ? AND phone_number = ?");
        $checkStmt->bind_param("ss", $name, $phone);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            throw new Exception("User with this name and phone number already exists.");
        }
        $checkStmt->close();

        // Step 2: Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Step 3: Insert new user
        $stmt = $mysqli->prepare("INSERT INTO Users (name, phone_number, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $hashedPassword);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $mysqli->commit();
        $message = "<div class='success'>✔️ User inserted successfully.</div>";
    } catch (Exception $e) {
        $mysqli->rollback();
        $err = htmlspecialchars($e->getMessage());
        $message = "<div class='error'>❌ Insert failed: {$err}</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Trigger Test</title>
  <style>
    body { font-family: Arial; margin: 20px; }
    .success { color: green; font-weight: bold; margin-top: 15px; }
    .error   { color: red;   font-weight: bold; margin-top: 15px; }
    form { max-width: 400px; }
    label { display: block; margin-bottom: 10px; }
    input[type="text"], input[type="password"] {
      width: 100%; padding: 8px; box-sizing: border-box;
    }
    button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="top-nav">
      <a href="/user">Homepage</a>
  </div>
  <h2>Trigger Test: User Insert</h2>
  <p>This trigger requires that <strong>phone_number</strong> is not null for any new user.<br>
     Also prevents duplicate users and stores passwords securely.</p>

  <form method="POST">
    <label>
      Name:
      <input type="text" name="name" required>
    </label>
    <label>
      Phone Number:
      <input type="text" name="phone" required>
    </label>
    <label>
      Password:
      <input type="password" name="password" required>
    </label>
    <button type="submit">Insert User</button>
  </form>

  <?= $message ?>

</body>
</html>
