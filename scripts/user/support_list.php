<?php
require_once __DIR__ . "/../includes/mongo_connect.php";

$errorMsg  = $mongoError;
$usernames = [];
$tickets   = [];

if (!$errorMsg) {
    try {
        $cmd    = new MongoDB\Driver\Command([
            'distinct' => 'tickets',
            'key'      => 'username',
            'query'    => ['status' => true],
        ]);
        $cursor = $manager->executeCommand('marketplace', $cmd);
        $res    = current($cursor->toArray());
        $usernames = $res->values ?? [];
    } catch (Exception $e) {
        $errorMsg = "Error fetching usernames: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username'])) {
    $username = $_POST['username'];
    try {
        $query  = new MongoDB\Driver\Query([
            'username' => $username,
            'status'   => true
        ]);
        $cursor = $manager->executeQuery($ticketsCollection, $query);
        $tickets = $cursor->toArray();
    } catch (Exception $e) {
        $errorMsg = "Error fetching tickets: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Active Tickets</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h2, h3 {
      margin-bottom: 10px;
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
      padding: 15px;
      margin-bottom: 30px;
      max-width: 650px;
    }
    .item {
      border: 1px solid blue;
      padding: 12px;
      margin: 15px 0;
      background: #f6f8ff;
    }
    .item a {
      color: blue;
      text-decoration: underline;
      font-size: 1em;
    }
    .error {
      color: red;
      font-weight: bold;
    }
    form {
      margin-bottom: 25px;
    }
    select, button {
      padding: 6px;
      font-size: 1em;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
    <a class="nav-link" href="support_create.php">Create Ticket</a>
  </nav>

  <h2>Active Tickets</h2>

  <?php if ($errorMsg): ?>
    <div class="error"><?=htmlspecialchars($errorMsg)?></div>
  <?php endif; ?>

  <form method="post">
    <label for="username">Select Username:</label>
    <select name="username" id="username" required>
      <option value="">-- Select Username --</option>
      <?php foreach ($usernames as $u): ?>
        <option value="<?=htmlspecialchars($u)?>"<?=isset($username) && $username===$u?' selected':''?>>
          <?=htmlspecialchars($u)?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Select</button>
  </form>

  <?php if (isset($username)): ?>
    <div class="group">
      <h3>Results for username “<?=htmlspecialchars($username)?>”</h3>
      <?php if ($tickets): ?>
        <?php foreach ($tickets as $t): ?>
          <div class="item">
            <strong>Status:</strong> <?=$t->status ? "Active" : "Inactive"?><br>
            <strong>Message:</strong> <?=htmlspecialchars($t->body)?><br>
            <strong>Created At:</strong> <?=$t->created_at?><br>
            <a href="support_view.php?id=<?=$t->_id?>">View Details</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No active tickets for this user.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</body>
</html>
