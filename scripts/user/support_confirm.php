<?php
// user/support_confirm.php
$username = $_GET['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ticket Created</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    nav {
      margin-bottom: 20px;
    }
    .nav-link {
      margin-right: 15px;
      color: purple;
      text-decoration: none;
    }
    .nav-link:hover {
      text-decoration: underline;
    }
    .group {
      border: 1px solid black;
      padding: 18px;
      max-width: 420px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
    <a class="nav-link" href="support_list.php">View Tickets</a>
  </nav>
  <div class="group">
    <h2>Ticket Created</h2>
    <p>Your ticket for <strong><?=htmlspecialchars($username)?></strong> has been created successfully.</p>
  </div>
</body>
</html>
