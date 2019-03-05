<?php 
  define("accessChecker", TRUE);
  
  require "redirectIfNotLoggedIn.php";
  require "dbHelper.php";
  $dbHelper = new DBHelper($_SESSION["userID"]);
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

<h1>Recommendations</h1>
<?php
    $userID = $_SESSION["userID"];
    $favorite = $dbHelper->fetch_auctionID_from_bids($userID);
    foreach ($favorite as $result){
        echo $result["categoryID"],"</br>";
    } 
    unset($result);
    ?>
</body>

</html>