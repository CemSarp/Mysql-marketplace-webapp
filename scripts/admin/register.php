<?php
// Enable error display
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../includes/db_connect.php";

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim inputs
    $username     = trim($_POST['username']);
    $age          = trim($_POST['age']);
    $phone_number = trim($_POST['phone_number']);
    $password     = $_POST['password'];

    // Validate all fields filled
    if (!$username || !$age || !$phone_number || !$password) {
        $message = "Please fill in all fields.";
    } else {
        // Check for duplicate username
        $stmt = $mysqli->prepare("SELECT userid FROM Users WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Username already taken. Choose another.";
        } else {
            // Compute next userid
            $res   = $mysqli->query("SELECT MAX(userid) AS maxid FROM Users");
            $row   = $res->fetch_assoc();
            $nextid = $row['maxid'] ? $row['maxid'] + 1 : 1;

            // Hash the password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user row
            $ins = $mysqli->prepare("
                INSERT INTO Users(userid, name, age, phone_number, password)
                VALUES (?, ?, ?, ?, ?)
            ");
            $ins->bind_param("isiss", $nextid, $username, $age, $phone_number, $hash);
            if ($ins->execute()) {
                // Redirect to login with flag
                header("Location: login.php?registered=1");
                exit;
            } else {
                $message = "Database error: " . $ins->error;
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
</head>
<body>
  <h2>Register New Account</h2>
  <?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post">
    <p>
      <label>Username:
        <input type="text" name="username" required>
      </label>
    </p>
    <p>
      <label>Age:
        <input type="number" name="age" required>
      </label>
    </p>
    <p>
      <label>Phone Number:
        <input type="text" name="phone_number" required>
      </label>
    </p>
    <p>
      <label>Password:
        <input type="password" name="password" required>
      </label>
    </p>
    <button type="submit">Register</button>
  </form>

  <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
