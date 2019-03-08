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
  <link rel="stylesheet" href="css/table.css" />
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

  <h1 style="margin: 15px 0px 15px 0px">Recommendations</h1>
  <?php
    $recommendations = $dbHelper->fetch_recommendations();
    if ($recommendations) {
      // Get the highest bid for each item auction that was returned
      for ($i = 0; $i < count($recommendations); $i++) {
        $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($recommendations[$i]["auctionID"]);
        $recommendations[$i] = array_merge($recommendations[$i], $highestBidInfo);
      
      }
       
      // HTML for the table to assign column headers
      echo "<table cellspacing='2' cellpadding='2'> 
        <tr>
          <th>Item Name</th> 
          <th>Item Description</th>
          <th>Start Price</th> 
          <th>Reserve Price</th> 
          <th>Start Datetime</th>
          <th>End Datetime</th> 
          <th>Highest Bid</th>
          <th>Recommendation Datetime</th>
        </tr>";

      // Populate the table with the row data
      foreach ($recommendations as $row) {         
        echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
          <td>" . $row["itemName"] . "</td> 
          <td>" . $row["description"] . "</td> 
          <td>£" . number_format($row["startPrice"]/100, 2) . "</td>
          <td>£" . number_format($row["reservePrice"]/100, 2) . "</td>
          <td>" . $row["startDatetime"] . "</td>
          <td>" . $row["endDatetime"] . "</td>
          <td>£" . number_format($row["highestBid"]/100, 2) . "</td>
          <td>" . $row["dateOfRecommendation"] . "</td>
        </tr>";
      }

      // Free up the memory used by the array
      unset($recommendations);
    } else {
        echo "No recommendations yet";
    }
  ?>

  <script type="text/javascript" src="js/table.js"></script>
</body>

</html>