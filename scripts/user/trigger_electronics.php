<?php
require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

/** Helper to get next PK */
function getNextId($mysqli, $table, $col) {
    $res = $mysqli->query("SELECT MAX($col) AS m FROM `$table`");
    $row = $res->fetch_assoc();
    return ($row['m'] !== null) ? ((int)$row['m'] + 1) : 1;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isValid = isset($_POST['valid']);

    try {
        $mysqli->begin_transaction();

        // 1) Create valid Date
        $date_id = getNextId($mysqli, 'Dates', 'date_id');
        $y = date('Y'); $mo = date('n'); $d = date('j');
        $mysqli->query("INSERT INTO Dates(date_id, year, month, day) VALUES ($date_id, $y, $mo, $d)");

        // 2) Create valid Price
        $price_id = getNextId($mysqli, 'Prices', 'price_id');
        $price = $isValid ? 500.00 : 100.00;
        $mysqli->query("INSERT INTO Prices(price_id, currency, amount) VALUES ($price_id, 'USD', $price)");

        // 3) Location ID (assume 1 exists or create it)
        $location_id = 1;

        // 4) Insert into Listings
        $uid = $_SESSION['userid'];
        $mysqli->query("
            INSERT INTO Listings(userid, headline, description, location_id, date_id, price_id)
            VALUES ($uid, 'Trigger Electronics Test', 'Testing electronics trigger', $location_id, $date_id, $price_id)
        ");
        $lid = $mysqli->insert_id;

        // 5) Insert into Electronics
        if ($isValid) {
            $sql = "
                INSERT INTO Electronics(lid, category_id, insurance, eage, ebrand)
                VALUES ($lid, 3, 'Standard', 2, 'Samsung')
            ";
        } else {
            $sql = "
                INSERT INTO Electronics(lid, category_id, insurance, eage, ebrand)
                VALUES ($lid, 3, NULL, NULL, '')
            ";
        }

        if (! $mysqli->query($sql)) {
            throw new Exception($mysqli->error);
        }

        $mysqli->commit();

        $message = $isValid
            ? "<div class='success'>✔️ Valid insert succeeded (trigger allowed).</div>"
            : "<div class='error'>❌ Invalid insert unexpectedly succeeded!</div>";

    } catch (Exception $ex) {
        $mysqli->rollback();
        $err = htmlspecialchars($ex->getMessage());

        if (str_contains($err, 'Missing required specifications')) {
            $message = "<div class='error'>❌ Trigger blocked insert: Missing required specifications for electronics.</div>";
        } else {
            $message = "<div class='error'>❌ Database error: {$err}</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Electronics Trigger Test</title>
  <style>
    body { font-family: Arial; margin:20px; }
    nav { margin-bottom:20px; }
    .nav-link { margin-right:15px; color:purple; text-decoration:none; }
    .nav-link:hover { text-decoration:underline; }
    .group { border:1px solid #ccc; padding:18px; max-width:600px; background:#f9f9f9; }
    button {
      padding:8px 16px; margin-right:10px; font-size:1em;
      background:#28a745; color:#fff; border:none; border-radius:4px; cursor:pointer;
    }
    button:hover { background:#218838; }
    .success { color:green; font-weight:bold; margin-top:15px; }
    .error   { color:red;   font-weight:bold; margin-top:15px; }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
  </nav>

  <h2>Electronics Trigger Test</h2>
  <p><strong>Trigger:</strong> Rejects electronics listings missing insurance, age, or brand.</p>

  <div class="group">
    <form method="post">
      <button name="valid">Test VALID Insert (all specs)</button>
      <button name="invalid">Test INVALID Insert (missing specs)</button>
    </form>

    <?php if ($message) echo $message; ?>
  </div>
</body>
</html>
