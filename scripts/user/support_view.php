<?php
// user/support_view.php
require_once __DIR__ . "/../includes/mongo_connect.php";

$errorMsg = $mongoError;
$ticket   = null;
if (!$errorMsg) {
    try {
        $ticketId = new MongoDB\BSON\ObjectId($_GET['id']);
        $query    = new MongoDB\Driver\Query(['_id' => $ticketId]);
        $ticket   = current($manager->executeQuery($ticketsCollection, $query)->toArray());
        if (!$ticket) $errorMsg = "Ticket not found.";
    } catch (Exception $e) {
        $errorMsg = "Error fetching ticket: " . $e->getMessage();
    }
}

// Handle new comment
if (!$errorMsg && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment'] ?? '');
    if ($comment) {
        try {
            $newComment = [
                'username'   => $_POST['username'] ?? '',
                'body'       => $comment,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                ['_id' => $ticketId],
                ['$push' => ['comments' => $newComment]]
            );
            $manager->executeBulkWrite($ticketsCollection, $bulk);
            // Refresh
            $ticket = current($manager->executeQuery($ticketsCollection, $query)->toArray());
            $_POST['username'] = '';
            $_POST['comment']  = '';
        } catch (Exception $e) {
            $errorMsg = "Error adding comment: " . $e->getMessage();
        }
    } else {
        $errorMsg = "Comment cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Ticket</title>
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
      max-width: 520px;
      margin-bottom: 30px;
    }
    .comment-box {
      border: 1px solid blue;
      background: #f6f8ff;
      padding: 10px;
      margin-bottom: 10px;
    }
    .comment-meta {
      font-size: 0.92em;
      color: #333;
    }
    .error {
      color: red;
      font-weight: bold;
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }
    textarea {
      width: 97%;
      padding: 7px;
      font-size: 1em;
      font-family: Arial, sans-serif;
      border: 1px solid #888;
      border-radius: 4px;
      margin-bottom: 15px;
      margin-top: 3px;
      box-sizing: border-box;
    }
    button {
      padding: 8px 16px;
      font-size: 1em;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 5px;
    }
    button:hover {
      background: #1d7a32;
    }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
    <a class="nav-link" href="support_list.php">Tickets</a>
  </nav>

  <?php if ($errorMsg): ?>
    <div class="error"><?=htmlspecialchars($errorMsg)?></div>
  <?php elseif ($ticket): ?>
    <div class="group">
      <h2>Ticket Details</h2>
      <p><strong>Username:</strong> <?=htmlspecialchars($ticket->username)?></p>
      <p><strong>Created At:</strong> <?=$ticket->created_at?></p>
      <p><strong>Status:</strong> <?=$ticket->status ? "Active" : "Inactive"?></p>
      <p><strong>Message:</strong><br> <?=nl2br(htmlspecialchars($ticket->body))?></p>

      <h3>Comments</h3>
      <?php if (!empty($ticket->comments)): ?>
        <?php foreach ($ticket->comments as $c): ?>
          <div class="comment-box">
            <div class="comment-meta">
              <strong><?=htmlspecialchars($c->username)?></strong>
              <em>(<?=$c->created_at?>)</em>
            </div>
            <div><?=nl2br(htmlspecialchars($c->body))?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No comments yet.</p>
      <?php endif; ?>

      <h4>Add Comment</h4>
      <form method="post">
      <label>
        Username:
        <input
        type="text"
        name="username"
        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        required >
      </label>
      <label>
        Comment:
        <textarea name="comment" rows="3" required><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
        </label>
        <button type="submit">Post Comment</button>
      </form>
    </div>
  <?php endif; ?>
</body>
</html>
