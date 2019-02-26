<?php
  require "redirectIfNotLoggedIn.php";
  include "header.php";
  require "dbHelper.php";
  $userID = $_SESSION["userID"];
  $dbHelper = new DBHelper($userID);
?>

<!doctype html>
<html>

<head>
    <link rel="stylesheet" href="css/table.css">
</head>

<body>
    <?php        
        $salesHistory = $dbHelper -> fetch_sales_history($userID);
        if ($salesHistory) {
            // Get the highest bid for each auction that was won as the final price that was paid
            for ($i = 0; $i < count($salesHistory); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($salesHistory[$i]["auctionID"]);
                $salesHistory[$i] = array_merge($salesHistory[$i], $highestBidInfo);
            }

            // HTML for the table to assign column headers
            echo "<table cellspacing='2' cellpadding='2'> 
            <tr> 
                <th>Item Name</th> 
                <th>Item Description</th> 
                <th>Sale Datetime</th> 
                <th>Sale Price</th> 
                <th>Buyer ID</th> 
            </tr>";

            // Populate the table with the row data
            foreach ($salesHistory as $row) {         
                echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                          <td>" . $row["itemName"] . "</td> 
                          <td>" . $row["description"] . "</td> 
                          <td>" . $row["saleDate"] . "</td>
                          <td>£" . number_format($row["highestBid"]/100, 2) . "</td>
                          <td>" . $row["buyerID"] . "</td>
                      </tr>";
            }

            // Free up the memory used by the array
            unset($salesHistory);
        }
        else {
            echo '<h1>No purchase history</h1>';
        }
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>