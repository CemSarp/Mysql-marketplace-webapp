<?php
// user/support_create.php
require_once __DIR__ . "/../includes/mongo_connect.php";

$errorMsg = $mongoError;
$username = $_GET['username'] ?? '';

if (!$errorMsg && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $body     = trim($_POST['body']     ?? '');
  if (!$username || !$body) {
    $errorMsg = "Please provide both username and message.";
  } else {
    try {
      $ticket = [
        'username'   => $username,
        'body'       => $body,
        'created_at' => date("Y-m-d H:i:s"),
        'status'     => true,
        'comments'   => []
      ];
      $bulk = new MongoDB\Driver\BulkWrite;
      $bulk->insert($ticket);
      $manager->executeBulkWrite($ticketsCollection, $bulk);
      header("Location: support_confirm.php?username=" . urlencode($username));
      exit;
    } catch (Exception $e) {
      $errorMsg = "Error creating ticket: " . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Support Ticket</title>
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
      max-width: 480px;
      margin-bottom: 30px;
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    input[type="text"], textarea {
      width: 96%;
      padding: 7px;
      margin-top: 4px;
      margin-bottom: 18px;
      font-size: 1em;
      font-family: Arial, sans-serif;
      box-sizing: border-box;
      border: 1px solid #888;
      border-radius: 4px;
    }
    button {
      padding: 8px 16px;
      font-size: 1em;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background: #1d7a32;
    }
    .error {
      color: red;
      font-weight: bold;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
    <a class="nav-link" href="support_list.php">Tickets</a>
  </nav>

  <h2>Create Support Ticket</h2>

  <div class="group">
    <?php if ($errorMsg): ?>
      <div class="error"><?=htmlspecialchars($errorMsg)?></div>
    <?php endif; ?>

    <form method="post">
      <label>
        Username:
        <input type="text" name="username" required value="<?=htmlspecialchars($username)?>">
      </label>
      <label>
        Message:
        <textarea name="body" rows="4" required><?=htmlspecialchars($_POST['body'] ?? '')?></textarea>
      </label>
      <button type="submit">Submit Ticket</button>
    </form>
  </div>
</body>
</html>
