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

<body>
    <h1>Reccomadations</h1>
<?php
  session_start();
  $userID = $_SESSION["userID"];
  $auctionID = $dbHelper->fetch_auctionID_from_bids($userID);
  //$itemID = $dbHelper->fetch_itemid_from_auctions($auctionID);
  //$categoryID = $dbHelper->fetch_favorite_categories($itemID);
  // $result = $dbHelper->fetch_all_items_from_categoris($result);
  //$result = $dbHelper->fetch_popular_auctionID($categoryID);
  echo $userID,"</br>";
  echo print_r($auctionID),"</br>";
?>
</body>    
</html>