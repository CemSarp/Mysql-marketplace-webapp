<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace"; 

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli->begin_transaction();
    
    try {
        if (isset($_POST['test_valid'])) {
            
            
            
            $mysqli->query("INSERT INTO Listings(userid, headline, description, location_id, date_id, price_id) 
                           VALUES (1, 'Test House Listing', 'Testing house trigger', 1, 1, 1)");
            $test_lid = $mysqli->insert_id;
            
            // insert house with all required specs
            $sql = "INSERT INTO Houses(lid, category_id, m2, room_count, bage) 
                    VALUES ($test_lid, 1, 120.5, 4, 5)";
            
            if ($mysqli->query($sql)) {
                $message = "<div class='success'>✔️ Valid insert succeeded! House with all specifications was added (lid: $test_lid).</div>";
            }
            
        } elseif (isset($_POST['test_invalid'])) {
            // Test Case 2: Missing required fields - should fail
            
            
            $mysqli->query("INSERT INTO Listings(userid, headline, description, location_id, date_id, price_id) 
                           VALUES (1, 'Test House Listing 2', 'Testing house trigger failure', 1, 1, 1)");
            $test_lid = $mysqli->insert_id;
            
            // Try to insert house with missing specs m2 and bage are NULL
            $sql = "INSERT INTO Houses(lid, category_id, m2, room_count, bage) 
                    VALUES ($test_lid, 1, NULL, 3, NULL)";
            
            if ($mysqli->query($sql)) {
                $message = "<div class='error'>❌ Invalid insert unexpectedly succeeded! Trigger may not be working.</div>";
            }
        }
        
        // If we reach here without exceptions commit the transaction
        $mysqli->commit();
        
    } catch (Exception $ex) {
        // Rollback on any error
        $mysqli->rollback();
        
        $error = $ex->getMessage();
        
        // Check if it's our trigger's error message
        if (strpos($error, "Missing required specifications") !== false) {
            $message = "<div class='success'>✔️ Trigger working correctly! Insert was blocked due to missing specifications (m², room count, or building age).</div>";
        } else {
            $message = "<div class='error'>❌ Database error: " . htmlspecialchars($error) . "</div>";
        }
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>House Trigger Test</title>
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

  <h2>House Specifications Trigger Test</h2>
  <p>
    <strong>Trigger:</strong> Ensures every house listing includes area (m²), room count, and building age. Blocks any insert missing those specs.
  </p>

  <div class="group">
    <form method="post">
      <button name="test_valid">Test Valid Insert (all specs present)</button>
      <button name="test_invalid">Test Invalid Insert (missing specs)</button>
    </form>
    <?php if ($message) echo $message; ?>
  </div>
</body>
</html>
