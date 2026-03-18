<?php
require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

/** Get next PK for a table */
function getNextId($mysqli, $table, $col) {
    $res = $mysqli->query("SELECT MAX($col) AS m FROM `$table`");
    $row = $res->fetch_assoc();
    return ($row['m'] !== null) ? ((int)$row['m'] + 1) : 1;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isValid  = isset($_POST['valid']);
    $priceVal = $isValid ? 5000.00 : 200.00;

    try {
        $mysqli->begin_transaction();

        // Date
        $date_id = getNextId($mysqli, 'Dates', 'date_id');
        $y = date('Y'); $mo = date('n'); $d = date('j');
        $mysqli->query("INSERT INTO Dates(date_id,year,month,day)
                        VALUES($date_id,$y,$mo,$d)");

        // Price
        $price_id = getNextId($mysqli, 'Prices', 'price_id');
        $mysqli->query("INSERT INTO Prices(price_id,currency,amount)
                        VALUES($price_id,'USD',$priceVal)");

        // Location İzmir/Konak
        $city = 'İzmir'; $street = 'Konak';
        $stmt = $mysqli->prepare("
          SELECT location_id FROM Locations WHERE city=? AND street=? LIMIT 1
        ");
        $stmt->bind_param("ss",$city,$street);
        $stmt->execute();
        $resLoc = $stmt->get_result();
        if ($r = $resLoc->fetch_assoc()) {
            $location_id = $r['location_id'];
        } else {
            $location_id = getNextId($mysqli,'Locations','location_id');
            $mysqli->query("INSERT INTO Locations(location_id,city,street)
                            VALUES($location_id,'İzmir','Konak')");
        }
        $stmt->close();

        // Dynamic headline & description
        $headline    = $isValid
            ? 'TRIGGER TEST VALID INSERT'
            : 'TRIGGER TEST INVALID INSERT';
        $description = 'test insert, brand: BMW, address: İzmir, Konak';

        // Call stored procedure (always valid specs)
        $uid   = $_SESSION['userid'];
        $km    = 1000;
        $vbrand= 'BMW';
        $vage  = 1;
        $sql   = "
          CALL sp_create_vehicle_listing(
            $uid,
            '{$mysqli->real_escape_string($headline)}',
            '{$mysqli->real_escape_string($description)}',
            $location_id,
            $date_id,
            $price_id,
            $km,
            '{$mysqli->real_escape_string($vbrand)}',
            $vage
          )
        ";
        $mysqli->query($sql);
        while ($mysqli->more_results() && $mysqli->next_result()) {}

        $mysqli->commit();
        $message = $isValid
            ? "<div class='success'> Valid insert succeeded (trigger allowed).</div>"
            : "<div class='error'> Invalid insert unexpectedly succeeded!</div>";

    } catch (mysqli_sql_exception $ex) {
        $mysqli->rollback();
        $err = htmlspecialchars($ex->getMessage());
        $message = $isValid
            ? "<div class='error'>Unexpected error: {$err}</div>"
            : "<div class='error'>Trigger blocked insert: {$err}</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vehicle Trigger Test</title>
  <style>
    body { font-family: Arial; margin:20px; }
    nav { margin-bottom:20px; }
    .nav-link { margin-right:15px; color:purple; text-decoration:none; }
    .nav-link:hover { text-decoration:underline; }
    .group { border:1px solid black; padding:18px; max-width:600px; }
    button {
      padding:8px 16px; margin-right:10px; font-size:1em;
      background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;
    }
    button:hover { background:#0056b3; }
    .success { color:green; font-weight:bold; margin-top:15px; }
    .error   { color:red;   font-weight:bold; margin-top:15px; }
  </style>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
  </nav>

  <h2>Vehicle Price Trigger Test</h2>
  <p>
    <strong>Trigger:</strong> Rejects vehicle listings priced below 1000. Which is suspiciously low for an vehicle pricing.<br>
  </p>

  <div class="group">
    <form method="post">
      <button name="valid">Test Valid Insert (price = 5000)</button>
      <button name="invalid">Test Invalid Insert (price = 200)</button>
    </form>
    <?php if ($message) echo $message; ?>
  </div>
</body>
</html>
