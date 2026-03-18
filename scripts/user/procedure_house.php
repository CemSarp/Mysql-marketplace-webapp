<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

$message = "";

// City → Streets (must match DB values)
$locations = [
    'İstanbul' => ["Pendik", "Kadıköy", "Beşiktaş", "Üsküdar", "Şişli"],
    'İzmir'    => ["Karşıyaka", "Bornova", "Konak", "Buca", "Çiğli"],
    'Ankara'   => ["Çankaya", "Keçiören", "Mamak", "Sincan", "Etimesgut"],
    'Bursa'    => ["Osmangazi", "Nilüfer", "Yıldırım", "Mudanya", "Gemlik"],
    'Adana'    => ["Seyhan", "Çukurova", "Yüreğir", "Sarıçam", "Ceyhan"],
];

$currencies = ['TRY', 'USD', 'EUR'];

/** Helper to get next ID */
function getNextId($mysqli, $table, $col) {
    $res = $mysqli->query("SELECT MAX($col) AS m FROM $table");
    $row = $res->fetch_assoc();
    return ($row['m'] !== null) ? ((int)$row['m'] + 1) : 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headline    = trim($_POST['headline']     ?? '');
    $description = trim($_POST['description']  ?? '');
    $city        = $_POST['city']              ?? '';
    $street      = $_POST['street']            ?? '';
    $m2          = floatval($_POST['m2']       ?? -1);
    $rooms       = intval($_POST['rooms']      ?? -1);
    $bage        = intval($_POST['bage']       ?? -1);
    $priceAmount = floatval($_POST['price_amount'] ?? 0);
    $currency    = $_POST['currency']          ?? '';

    $valid = $headline && $description
          && isset($locations[$city])
          && in_array($street, $locations[$city], true)
          && $m2 > 0 && $rooms > 0 && $bage >= 0
          && $priceAmount > 0 && in_array($currency, $currencies, true);

    if (!$valid) {
        $message = "<p class='error'>Please fill in all fields correctly.</p>";
    } else {
        $stmt = $mysqli->prepare("SELECT location_id FROM Locations WHERE city = ? AND street = ?");
        $stmt->bind_param("ss", $city, $street);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!($row = $res->fetch_assoc())) {
            $message = "<p class='error'>Location not found in database.</p>";
        } else {
            $location_id = (int)$row['location_id'];
            $stmt->close();

            $mysqli->begin_transaction();
            try {
                $date_id = getNextId($mysqli, "Dates", "date_id");
                $y = date('Y'); $mo = date('n'); $d = date('j');
                $mysqli->query("INSERT INTO Dates(date_id, year, month, day) VALUES ($date_id, $y, $mo, $d)");

                $price_id = getNextId($mysqli, "Prices", "price_id");
                $cur = $mysqli->real_escape_string($currency);
                $mysqli->query("INSERT INTO Prices(price_id, currency, amount) VALUES ($price_id, '$cur', $priceAmount)");

                $uid  = $_SESSION['userid'];
                $hl   = $mysqli->real_escape_string($headline);
                $desc = $mysqli->real_escape_string($description);

                $mysqli->query(
                    "CALL sp_create_house_listing(
                        $uid,
                        '$hl',
                        '$desc',
                        $location_id,
                        $date_id,
                        $price_id,
                        $m2,
                        $rooms,
                        $bage
                    )"
                );

                while ($mysqli->more_results() && $mysqli->next_result()) {}
                $mysqli->commit();
                $message = "<p class='success'>House listing created!</p>";
                $_POST = [];
            } catch (Exception $e) {
                $mysqli->rollback();
                $message = "<p class='error'>Transaction failed: "
                         . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create House Listing</title>
  <style>
    body{font-family:Arial;margin:20px;}
    nav{margin-bottom:20px;}
    form{max-width:600px;}
    label{display:block;margin:8px 0;}
    input,textarea,select{width:100%;padding:8px;margin-bottom:15px;box-sizing:border-box;}
    button{padding:10px 15px;background:#007bff;color:#fff;border:none;cursor:pointer;}
    .success{color:green;} .error{color:red;}
  </style>
  <script>
  const locations = <?=json_encode($locations, JSON_UNESCAPED_UNICODE)?>;
  function updateStreets() {
      const city = document.querySelector('select[name="city"]').value;
      const streetSelect = document.querySelector('select[name="street"]');
      streetSelect.innerHTML = '<option value="">-- Select District --</option>';
      if (city && locations[city]) {
          locations[city].forEach(function(street) {
              const opt = document.createElement('option');
              opt.value = street;
              opt.textContent = street;
              streetSelect.appendChild(opt);
          });
      }
  }
  document.addEventListener('DOMContentLoaded', function() {
      document.querySelector('select[name="city"]').addEventListener('change', updateStreets);
      updateStreets();
  });
  </script>
</head>
<body>
  <nav>
    <a href="index.php">Homepage</a>
  </nav>

  <h2>Create House Listing</h2>
  <?= $message ?>

  <form method="post">
    <label>Headline:
      <input type="text" name="headline" value="<?=htmlspecialchars($_POST['headline'] ?? '')?>" required>
    </label>
    <label>Description:
      <textarea name="description" rows="4" required><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>
    </label>
    <label>City:
      <select name="city" required>
        <option value="">-- Select City --</option>
        <?php foreach ($locations as $c => $s): ?>
          <option value="<?=htmlspecialchars($c)?>" <?=($_POST['city'] ?? '') === $c ? 'selected' : ''?>><?=htmlspecialchars($c)?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>District:
      <select name="street" required>
        <option value="">-- Select District --</option>
      </select>
    </label>
    <label>Square Meters (m²):
      <input type="number" name="m2" step="0.1" min="1" value="<?=htmlspecialchars($_POST['m2'] ?? '')?>" required>
    </label>
    <label>Room Count:
      <input type="number" name="rooms" min="1" value="<?=htmlspecialchars($_POST['rooms'] ?? '')?>" required>
    </label>
    <label>Building Age:
      <input type="number" name="bage" min="0" value="<?=htmlspecialchars($_POST['bage'] ?? '')?>" required>
    </label>
    <label>Price:
      <input type="number" name="price_amount" step="0.01" min="0.01" value="<?=htmlspecialchars($_POST['price_amount'] ?? '')?>" required>
    </label>
    <label>Currency:
      <select name="currency" required>
        <option value="">-- Select Currency --</option>
        <?php foreach ($currencies as $cur): ?>
          <option value="<?=htmlspecialchars($cur)?>" <?=($_POST['currency'] ?? '') === $cur ? 'selected' : ''?>><?=htmlspecialchars($cur)?></option>
        <?php endforeach;?>
      </select>
    </label>
    <button type="submit">Create Listing</button>
  </form>
</body>
</html>