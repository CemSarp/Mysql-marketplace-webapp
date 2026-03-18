<?php
// Enable error display for debugging
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../includes/db_connect.php";

$message = "";
// Show registration success message
if (isset($_GET['registered'])) {
    $message = "Registration successful! Please log in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user by name
    $stmt = $mysqli->prepare("SELECT userid, name, password FROM Users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userid, $name, $hash);
        $stmt->fetch();
        // Verify hashed password
        if (password_verify($password, $hash)) {
            // Success: set session and redirect
            $_SESSION['userid']   = $userid;
            $_SESSION['username'] = $name;
            $_SESSION['role']     = 'user';
            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Invalid username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
</head>
<body>
  <h2>User Login</h2>
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
      <label>Password:
        <input type="password" name="password" required>
      </label>
    </p>
    <button type="submit">Login</button>
  </form>

  <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
