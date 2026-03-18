<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

$message = "";

$locations = [
       'İstanbul'  => ["Pendik", "Kadıköy", "Beşiktaş", "Üsküdar", "Şişli"],
    'İzmir'     => ["Karşıyaka", "Bornova", "Konak", "Buca", "Çiğli"],
    'Ankara'    => ["Çankaya", "Keçiören", "Mamak", "Sincan", "Etimesgut"],
    'Bursa'     => ["Osmangazi", "Nilüfer", "Yıldırım", "Mudanya", "Gemlik"],
    'Adana'     => ["Seyhan", "Çukurova", "Yüreğir", "Sarıçam", "Ceyhan"],
];
$currencies = ['TRY', 'USD', 'EUR'];
$brands = ["Samsung", "Apple", "Sony", "LG", "Huawei"];
$insurances = ["None", "Standard", "Extended"];

function getNextId($mysqli, $table, $col) {
    $res = $mysqli->query("SELECT MAX($col) AS m FROM $table");
    $row = $res->fetch_assoc();
    return ($row['m'] !== null) ? ((int)$row['m'] + 1) : 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headline    = trim($_POST['headline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city        = $_POST['city'] ?? '';
    $street      = $_POST['street'] ?? '';
    $ebrand      = trim($_POST['ebrand'] ?? '');
    $eage        = intval($_POST['eage'] ?? -1);
    $insurance   = $_POST['insurance'] ?? '';
    $priceAmount = floatval($_POST['price_amount'] ?? 0);
    $currency    = $_POST['currency'] ?? '';

    $valid = $headline && $description
          && isset($locations[$city])
          && in_array($street, $locations[$city], true)
          && in_array($ebrand, $brands, true)
          && $eage >= 0
          && $priceAmount > 0
          && in_array($currency, $currencies, true)
          && in_array($insurance, $insurances, true);

    if (!$valid) {
        $message = "<p class='error'>Please fill all fields correctly.</p>";
    } else {
        $stmt = $mysqli->prepare(
            "SELECT location_id FROM Locations WHERE city = ? AND street = ?"
        );
        $stmt->bind_param("ss", $city, $street);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!($row = $res->fetch_assoc())) {
            $message = "<p class='error'>Location not found.</p>";
        } else {
            $location_id = (int)$row['location_id'];
            $stmt->close();

            $mysqli->begin_transaction();
            try {
                $date_id = getNextId($mysqli, "Dates", "date_id");
                $y = date('Y'); $mo = date('n'); $d = date('j');
                if (!$mysqli->query("INSERT INTO Dates(date_id,year,month,day) VALUES($date_id,$y,$mo,$d)"))
                    throw new Exception($mysqli->error);

                $price_id = getNextId($mysqli, "Prices", "price_id");
                if (!$mysqli->query(
                    "INSERT INTO Prices(price_id,currency,amount)
                     VALUES($price_id,'{$mysqli->real_escape_string($currency)}',$priceAmount)"
                )) throw new Exception($mysqli->error);

                $uid = $_SESSION['userid'];
                $hl = $mysqli->real_escape_string($headline);
                $desc = $mysqli->real_escape_string($description);
                $ins = $mysqli->real_escape_string($insurance);
                $br = $mysqli->real_escape_string($ebrand);

                if (!$mysqli->query(
                    "CALL sp_create_electronics_listing(
                        $uid, '$hl', '$desc', $location_id, $date_id, $price_id, '$ins', $eage, '$br'
                    )"
                )) throw new Exception($mysqli->error);

                while ($mysqli->more_results() && $mysqli->next_result()) {}

                $mysqli->commit();
                $message = "<p class='success'>Electronics listing created!</p>";
                $_POST = [];

            } catch (Exception $e) {
                $mysqli->rollback();
                $message = "<p class='error'>Transaction failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Electronics Listing</title>
<style>
  body{font-family:Arial;margin:20px;}
  form{max-width:600px;}
  label{display:block;margin:8px 0;}
  input,textarea,select{width:100%;padding:8px;margin-bottom:15px;box-sizing:border-box;}
  button{padding:10px 15px;background:#28a745;color:#fff;border:none;cursor:pointer;}
  .success{color:green;} .error{color:red;}
</style>
<script>
const locations = <?=json_encode($locations, JSON_UNESCAPED_UNICODE)?>;
function updateStreets() {
    const city = document.querySelector('select[name="city"]').value;
    const streetSelect = document.querySelector('select[name="street"]');
    streetSelect.innerHTML = '<option value="">-- Select Street --</option>';
    if(city && locations[city]) {
        locations[city].forEach(street => {
            const opt = document.createElement('option');
            opt.value = street;
            opt.textContent = street;
            streetSelect.appendChild(opt);
        });
    }
    <?php if (isset($_POST['street'])): ?>
    streetSelect.value = <?=json_encode($_POST['street'])?>;
    <?php endif; ?>
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('select[name="city"]').addEventListener('change', updateStreets);
    updateStreets();
});
</script>
</head>
<body>
<nav><a href="index.php">Homepage</a></nav>
<h2>Create Electronics Listing</h2>
<p>All fields are required.</p>
<?= $message ?>
<form method="post">
  <label>Headline:
    <input type="text" name="headline" required value="<?=htmlspecialchars($_POST['headline'] ?? '')?>">
  </label>
  <label>Description:
    <textarea name="description" rows="4" required><?=htmlspecialchars($_POST['description'] ?? '')?></textarea>
  </label>
  <label>City:
    <select name="city" required>
      <option value="">-- Select City --</option>
      <?php foreach ($locations as $c => $streets): ?>
        <option value="<?=htmlspecialchars($c)?>" <?=($_POST['city']??'')===$c?'selected':''?>><?=htmlspecialchars($c)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>District:
    <select name="street" required>
      <option value="">-- Select District --</option>
    </select>
  </label>
  <label>Brand:
    <select name="ebrand" required>
      <option value="">-- Select Brand --</option>
      <?php foreach ($brands as $b): ?>
        <option value="<?=htmlspecialchars($b)?>" <?=($_POST['ebrand']??'')===$b?'selected':''?>><?=htmlspecialchars($b)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Age (years):
    <input type="number" name="eage" min="0" required value="<?=htmlspecialchars($_POST['eage'] ?? '')?>">
  </label>
  <label>Insurance:
    <select name="insurance" required>
      <option value="">-- Select Insurance --</option>
      <?php foreach ($insurances as $ins): ?>
        <option value="<?=htmlspecialchars($ins)?>" <?=($_POST['insurance']??'')===$ins?'selected':''?>><?=htmlspecialchars($ins)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Price:
    <input type="number" name="price_amount" step="0.01" min="0.01" required value="<?=htmlspecialchars($_POST['price_amount'] ?? '')?>">
  </label>
  <label>Currency:
    <select name="currency" required>
      <option value="">-- Select Currency --</option>
      <?php foreach ($currencies as $cur): ?>
        <option value="<?=htmlspecialchars($cur)?>" <?=($_POST['currency']??'')===$cur?'selected':''?>><?=htmlspecialchars($cur)?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button type="submit">Create Listing</button>
</form>
</body>
</html>
