<?php
require_once __DIR__ . "/../includes/auth_user.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h2 {
      margin-bottom: 10px;
    }
    .group {
      border: 1px solid black;
      padding: 10px;
      margin-bottom: 30px;
    }
    .item {
      border: 1px solid blue;
      padding: 10px;
      margin: 10px 0;
    }
    .item a {
      display: inline-block;
      margin-top: 5px;
      color: blue;
      text-decoration: underline;
    }
    .footer-links {
      margin-top: 20px;
    }
    .footer-links a {
      margin-right: 15px;
      color: purple;
      text-decoration: none;
    }
    .footer-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>

  <!-- Triggers Section -->
  <h2>Triggers:</h2>
  <div class="group">

    <div class="item">
      <strong>Trigger 1 (by Cem Sarp Takım):</strong>
      This trigger ensures that vehicle listing prices are higher than 1000. In order the eleminate meaningless priced listings.<br>
      <a href="trigger_vehicle.php">Go to the trigger’s page</a>
    </div>

    <div class="item">
      <strong>Trigger 2 (by Petek Metin):</strong>
      This trigger ensures <strong>square meters (m²)</strong>, <strong>room count</strong>, and <strong>building age</strong> are present before inserting a house.<br>
      <a href="trigger_house.php">Go to the trigger’s page</a>
    </div>

    <div class="item">
      <strong>Trigger 3 (by Başar Erses):</strong>
      This trigger ensures every user has a <strong>phone number</strong> before being inserted into the database.</br>
      <a href="trigger_user.php">Go to the trigger’s page</a>
    </div>

    <div class="item">
      <strong>Trigger 4 (by Ege Derman):</strong>
      This trigger ensures that insurance, age, and brand information are present before inserting an electronics listing.<br>
      <a href="trigger_electronics.php">Go to the trigger’s page</a>
    </div>

  </div>

</div>

  <!-- Stored Procedures Section -->
  <h2>Stored Procedures:</h2>
  <div class="group">

    <div class="item">
      <strong>Vehicle Listing Procedure (by Cem Sarp):</strong>
      Inserts a new listing into Listings and Vehicles tables via a stored procedure.<br>
      <a href="procedure_vehicle.php">Go to the procedure’s page</a>
    </div>

    <div class="item">
      <strong>House Listing Procedure (by Petek Metin):</strong>
      Inserts a new listing into Listings and Houses tables via a stored procedure.<br>
      <a href="procedure_house.php">Go to the procedure’s page</a>
    </div>

    <div class="item">
      <strong>Listing Report Procedure (by Başar Erses):</strong>
      Generates a categorized report of listings (Vehicle, House, Electronics) by joining the appropriate tables.<br>
      <a href="procedure_listing.php">Go to the procedure’s page</a>
    </div>

    <div class="item">
      <strong>Electronics Listing Procedure (by Ege Derman):</strong>
      Inserts a new listing into Listings and Electronics tables via a stored procedure.<br>
      <a href="procedure_electronics.php">Go to the procedure’s page</a>
    </div>

</div>

  <!-- Footer Links -->
  <div class="footer-links">
    <a href="support_list.php">Support</a>
    <a href="logout.php">Logout</a>
  </div>
</body>
</html>
