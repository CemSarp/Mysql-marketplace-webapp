<?php
require_once __DIR__ . "/../includes/auth_admin.php";
require_once __DIR__ . "/../includes/mongo_connect.php";

$query  = new MongoDB\Driver\Query(['status' => true]);
$cursor = $manager->executeQuery($ticketsCollection, $query);
$docs   = $cursor->toArray();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>All Tickets</title></head>
<body>
    <h2>All Active Tickets</h2>
    <div style="border:1px solid black; padding:10px; margin:10px;">
        <h3>Results:</h3>
        <?php if ($docs): ?>
            <?php foreach ($docs as $d): ?>
                <div style="border:1px solid blue; padding:10px; margin:5px;">
                    <strong>User:</strong> <?=htmlspecialchars($d->username)?><br>
                    <strong>Created:</strong> <?=$d->created_at?><br>
                    <strong>Message:</strong> <?=htmlspecialchars($d->body)?><br>
                    <a href="ticket_view.php?id=<?=$d->_id?>">View / Manage</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active tickets.</p>
        <?php endif; ?>
    </div>
      <!-- Footer Links -->
    <div class="footer-links">
      <a href="logout.php"> Logout</a>
    </div>
</body>
</html>
