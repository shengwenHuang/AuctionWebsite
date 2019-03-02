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
  <div id="body-content">
      <?php if (isset($_GET['message'])): ?>
        <div class="error">
          <h1><?php echo $_GET['message']; ?></h1>
        </div>
      <?php endif;?>
  </div>

  <h1>My Homepage</h1>
</body>

<head>
    <p>Please a search term (minimum 3 characters)</p>
    <title>Search</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
    <form action="search.php" method="GET">
        <input type="text" name="query" />
        <input type="submit" value="Search" />
        <div class="inner-form">
          <div class="input-field first-wrap">
            <div class="input-select">
              <select data-trigger="" name="choices">
                <option placeholder="">Category</option>
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
</body>


</html>


