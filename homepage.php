<?php 
  define("accessChecker", TRUE);
  
  require "redirectIfNotLoggedIn.php";
  require "dbHelper.php";
  $dbHelper = new DBHelper(null);
  require "header.php";
?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>EbayLite</title>
  <!-- <link rel="stylesheet" href="css/style.css" type="text/css"> -->
</head>

<body>
  <h1>My Homepage</h1>
</body>

<head>
  <p>Please a search term (minimum 3 characters)</p>
  <title>Search</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
  <form action="process.php" method="POST">
    <input type="text" name="query" />
    <input name="search-button" type="submit" value="Search" />
    <div class="inner-form">
      <div class="input-field first-wrap">
        <div class="input-select">
          <select name="choices">
            <option>Category</option>
            <?php
              $result = $dbHelper -> get_catagories();
              if ($result) {
                foreach ($result as $row) {
                  echo '<option>'.$row["categoryName"].'</option>';
                }
              }
            ?>
          </select>
        </div>
      </div>
    </div>
  </form>

  <?php
    if (isset($_GET["message"])) {
      echo "<h3>" . $_GET["message"] . "</h3>";
    }
  ?>
</body>


</html>