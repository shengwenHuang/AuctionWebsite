<?php
  include "header.php";
  require "dbHelper.php";
  $userID = 2;
  $dbHelper = new DBHelper($userID);
?>

<!doctype html>
<html>

<body>
    <?php        
        $salesHistory = $dbHelper -> fetch_sales_history($userID);
        if ($salesHistory) {
            // Get the highest bid for each auction that was won as the final price that was paid
            for ($i = 0; $i < count($salesHistory); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($salesHistory[$i]["auctionID"]);
                $salesHistory[$i] = array_merge($salesHistory[$i], $highestBidInfo);
            }

            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Sale Datetime</font> </td>
                <td> <font face="Arial">Sale Price</font> </td>
                <td> <font face="Arial">Buyer ID</font> </td>
            </tr>';

            foreach ($salesHistory as $row) {
                echo '<tr>
                <td>' . $row["itemName"] . '</td>
                <td>' . $row["description"] . '</td>
                <td>' . $row["saleDate"] . '</td>
                <td>£' . number_format($row["highestBid"]/100, 2) . '</td>
                <td>' . $row["buyerID"] . '</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No purchase history</h1>';
        }
    ?>
</body>

</html>