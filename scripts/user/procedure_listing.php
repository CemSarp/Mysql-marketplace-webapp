<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../includes/auth_user.php";
require_once __DIR__ . "/../includes/db_connect.php";

$category = "";
$reportResults = [];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = $_POST["category"] ?? "";
    if ($category !== "") {
        $stmt = $mysqli->prepare("CALL sp_generate_listing_report(?)");
        $stmt->bind_param("s", $category);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $reportResults = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $error = "Failed to generate report: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please select a category.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listing Report</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }
        .top-nav {
            margin-bottom: 20px;
        }
        .top-nav a {
            text-decoration: none;
            color: #333;
            background-color: #eee;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="top-nav">
        <a href="/user">Homepage</a>
    </div>

    <h2>Generate Classified Listing Report</h2>
    <form method="POST">
        <label for="category">Select Category:</label>
        <select name="category" id="category">
            <option value="">--Choose--</option>
            <option value="Vehicle" <?php if ($category === "Vehicle") echo "selected"; ?>>Vehicle</option>
            <option value="House" <?php if ($category === "House") echo "selected"; ?>>House</option>
            <option value="Electronics" <?php if ($category === "Electronics") echo "selected"; ?>>Electronics</option>
        </select>
        <button type="submit">Generate</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($reportResults)): ?>
        <h3>Results:</h3>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <?php foreach (array_keys($reportResults[0]) as $col): ?>
                        <th><?php echo htmlspecialchars($col); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportResults as $row): ?>
                    <tr>
                        <?php foreach ($row as $val): ?>
                            <td><?php echo htmlspecialchars($val); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
