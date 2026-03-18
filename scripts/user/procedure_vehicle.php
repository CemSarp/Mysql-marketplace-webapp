<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Require authentication and database connection
require_once __DIR__ . '/../includes/auth_user.php';
require_once __DIR__ . '/../includes/db_connect.php';

$message = '';

// --- Static dropdown data ---
$locations = [
    'İstanbul' => ['Pendik','Kadıköy','Beşiktaş','Üsküdar','Şişli'],
    'İzmir'    => ['Karşıyaka','Bornova','Konak','Buca','Çiğli'],
    'Ankara'   => ['Çankaya','Keçiören','Mamak','Sincan','Etimesgut'],
    'Bursa'    => ['Osmangazi','Nilüfer','Yıldırım','Mudanya','Gemlik'],
    'Adana'    => ['Seyhan','Çukurova','Yüreğir','Sarıçam','Ceyhan'],
    'Mersin'   => ['Akdeniz','Mezitli','Toroslar','Yenişehir','Tarsus'],
];
$currencies = ['TRY', 'USD', 'EUR'];
$brands     = ['Renault','Fiat','Ford','Volkswagen','Toyota','Opel','Hyundai','Peugeot','Honda','Mercedes-Benz','BMW','Citroën','Nissan','Audi','Kia','Audi'];

// Helper: get the next integer ID
function getNextId($mysqli, $table, $col) {
    $row = $mysqli
        ->query("SELECT COALESCE(MAX($col),0)+1 AS next_id FROM $table")
        ->fetch_assoc();
    return (int)$row['next_id'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs
    $headline    = trim($_POST['headline']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $city        = $_POST['city']             ?? '';
    $street      = $_POST['street']           ?? '';
    $brand       = $_POST['vbrand']           ?? '';
    $km          = intval($_POST['km']        ?? -1);
    $age         = intval($_POST['vage']      ?? -1);
    $price       = floatval($_POST['price_amount'] ?? 0);
    $currency    = $_POST['currency']         ?? '';

    // Validate inputs
    if (!$headline || !$description
        || !isset($locations[$city])
        || !in_array($street, $locations[$city], true)
        || !in_array($brand, $brands, true)
        || $km < 0 || $age < 0 || $price <= 0
        || !in_array($currency, $currencies, true)) {
        $message = '<p class="error">Please fill in all fields correctly.</p>';
    } else {
        // Lookup location_id
        $stmt = $mysqli->prepare('SELECT location_id FROM Locations WHERE city=? AND street=?');
        $stmt->bind_param('ss', $city, $street);
        $stmt->execute();
        $stmt->bind_result($location_id);

        if (!$stmt->fetch()) {
            $message = '<p class="error">Selected location not found.</p>';
            $stmt->close();
        } else {
            $stmt->close();
            // Begin transaction
            $mysqli->begin_transaction();
            try {
                // Insert date
                $date_id = getNextId($mysqli, 'Dates', 'date_id');
                list($Y,$M,$D) = explode('-', date('Y-n-j'));
                $mysqli->query("INSERT INTO Dates(date_id,year,month,day) VALUES($date_id,$Y,$M,$D)");

                // Insert price
                $price_id = getNextId($mysqli, 'Prices', 'price_id');
                $safeCur = $mysqli->real_escape_string($currency);
                $mysqli->query("INSERT INTO Prices(price_id,currency,amount) VALUES($price_id,'$safeCur',$price)");

                // Call stored procedure
                $userId = (int)$_SESSION['userid'];
                $proc = $mysqli->prepare('CALL sp_create_vehicle_listing(?,?,?,?,?,?,?,?,?)');
                $proc->bind_param('issiiiiss',
                    $userId, $headline, $description,
                    $location_id, $date_id, $price_id,
                    $km, $brand, $age
                );
                $proc->execute();
                $proc->close();

                // Flush any extra result sets
                while ($mysqli->more_results()) {
                    $mysqli->next_result();
                }
                $mysqli->commit();

                $message = '<p class="success">Vehicle listing created successfully.</p>';
                $_POST = [];
            } catch (Exception $e) {
                // Flush any leftover result sets before rollback
                while ($mysqli->more_results()) {
                    $mysqli->next_result();
                }
                $mysqli->rollback();
                $message = '<p class="error">Error: '.htmlspecialchars($e->getMessage()).'</p>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Vehicle Listing</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .error { color: red; }
    .success { color: green; }
    form { max-width: 600px; }
    label { display: block; margin: 10px 0; }
    input, textarea, select { width: 100%; padding: 8px; box-sizing: border-box; }
    button { padding: 10px 15px; }
  </style>
  <script>
    const cities = <?php echo json_encode($locations, JSON_UNESCAPED_UNICODE); ?>;
    document.addEventListener('DOMContentLoaded', () => {
      const cityEl = document.querySelector('[name="city"]');
      const streetEl = document.querySelector('[name="street"]');
      cityEl.addEventListener('change', () => {
        streetEl.innerHTML = '<option value="">-- Select District --</option>';
        (cities[cityEl.value]||[]).forEach(d => streetEl.add(new Option(d,d)));
        streetEl.value = '<?php echo addslashes($_POST['street']??''); ?>';
      });
      cityEl.dispatchEvent(new Event('change'));
    });
  </script>
</head>
<body>
  <nav>
    <a class="nav-link" href="index.php">Homepage</a>
  </nav>
  <h2>Create Vehicle Listing</h2>
  <?php echo $message; ?>
  <form method="post">
    <label>Headline
      <input type="text" name="headline" value="<?php echo htmlspecialchars($_POST['headline']??''); ?>" required>
    </label>
    <label>Description
      <textarea name="description" rows="4" required><?php echo htmlspecialchars($_POST['description']??''); ?></textarea>
    </label>
    <label>City
      <select name="city" required>
        <option value="">-- Select City --</option>
        <?php foreach ($locations as $c=>$list): ?>
          <option value="<?php echo $c;?>" <?php echo (($_POST['city']??'')===$c)?'selected':''; ?>><?php echo $c;?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>District
      <select name="street" required><option value="">-- Select District --</option></select>
    </label>
    <label>Brand
      <select name="vbrand" required>
        <option value="">-- Select Brand --</option>
        <?php foreach ($brands as $b): ?>
          <option value="<?php echo $b;?>" <?php echo (($_POST['vbrand']??'')===$b)?'selected':''; ?>><?php echo $b;?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Kilometers
      <input type="number" name="km" min="0" value="<?php echo htmlspecialchars($_POST['km']??''); ?>" required>
    </label>
    <label>Age (years)
      <input type="number" name="vage" min="0" value="<?php echo htmlspecialchars($_POST['vage']??''); ?>" required>
    </label>
    <label>Price
      <input type="number" name="price_amount" step="0.01" min="0.01" value="<?php echo htmlspecialchars($_POST['price_amount']??''); ?>" required>
    </label>
    <label>Currency
      <select name="currency" required>
        <option value="">-- Select Currency --</option>
        <?php foreach($currencies as $cur): ?>
          <option value="<?php echo $cur;?>" <?php echo (($_POST['currency']??'')===$cur)?'selected':''; ?>><?php echo $cur;?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button type="submit">Create Listing</button>
  </form>
</body>
</html>
