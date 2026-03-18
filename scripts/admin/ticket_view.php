<?php
require_once __DIR__ . "/../includes/auth_admin.php";
require_once __DIR__ . "/../includes/mongo_connect.php";

$adminUser = "admin";
$ticketId  = new MongoDB\BSON\ObjectId($_GET['id']);
$query     = new MongoDB\Driver\Query(['_id' => $ticketId]);
$ticket    = current($manager->executeQuery($ticketsCollection, $query)->toArray());
if (!$ticket) {
    die("Ticket not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resolve_submit'])) {
        // Mark resolved
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => $ticketId],
            ['$set' => ['status' => false]]
        );
        $manager->executeBulkWrite($ticketsCollection, $bulk);
    }
    if (isset($_POST['comment_submit']) && !empty($_POST['comment'])) {
        // Add admin comment
        $adminComment = [
            'username'   => $adminUser,
            'body'       => $_POST['comment'],
            'created_at' => date("Y-m-d H:i:s")
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['_id' => $ticketId],
            ['$push' => ['comments' => $adminComment]]
        );
        $manager->executeBulkWrite($ticketsCollection, $bulk);
    }
    // Refresh
    $ticket = current($manager->executeQuery($ticketsCollection, $query)->toArray());
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Manage Ticket</title></head>
<body>
    <h2>Ticket Details (Admin)</h2>
    <p><strong>Username:</strong> <?=htmlspecialchars($ticket->username)?></p>
    <p><strong>Status:</strong> <?=$ticket->status ? "Active" : "Resolved"?></p>
    <p><strong>Created At:</strong> <?=$ticket->created_at?></p>
    <p><strong>Message:</strong> <?=htmlspecialchars($ticket->body)?></p>

    <div style="border:1px solid black; padding:10px; margin:10px;">
      <h3>Comments:</h3>
      <?php if (!empty($ticket->comments)): ?>
        <?php foreach ($ticket->comments as $c): ?>
          <div style="border:1px solid blue; padding:10px; margin:5px;">
            <strong>Created At:</strong> <?= $c->created_at ?><br>
            <strong>Username:</strong> <?= htmlspecialchars($c->username) ?><br>
            <strong>Comment:</strong> <?= htmlspecialchars($c->body) ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No comments yet.</p>
      <?php endif; ?>
    </div>

    <?php if ($ticket->status): ?>
        <h4>Add Comment</h4>
        <form method="post">
            <textarea name="comment" rows="3" cols="50" required></textarea><br>
            <button name="comment_submit">Post Comment</button>
        </form>
        <form method="post" style="margin-top:10px;">
            <button name="resolve_submit">Mark as Resolved</button>
        </form>
    <?php else: ?>
        <p><em>This ticket is resolved.</em></p>
    <?php endif; ?>

    <div class="footer-links">
      <a href="tickets.php">All Tickets</a>
    </div>

</body>
</html>
