<?php
  include "redirectIfNotLoggedIn.php";
  include "header.php";
  require "dbHelper.php";
  $userID = 2;
  $dbHelper = new DBHelper($userID);
?>

<!doctype html>
<html>

<body>
    <?php
        $purchaseHistory = $dbHelper -> fetch_purchase_history($userID);
        if ($purchaseHistory) {
            // Get the highest bid for each auction that was won as the final price that was paid
            for ($i = 0; $i < count($purchaseHistory); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($purchaseHistory[$i]["auctionID"]);
                $purchaseHistory[$i] = array_merge($purchaseHistory[$i], $highestBidInfo);
            }

            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Purchase Datetime</font> </td>
                <td> <font face="Arial">Purchase Price</font> </td>
                <td> <font face="Arial">Seller ID</font> </td>
            </tr>';

            foreach ($purchaseHistory as $row) {
                echo '<tr>
                <td>' . $row["itemName"] . '</td>
                <td>' . $row["description"] . '</td>
                <td>' . $row["purchaseDate"] . '</td>
                <td>£' . number_format($row["highestBid"]/100, 2) . '</td>
                <td>' . $row["sellerID"] . '</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No purchase history</h1>';
        }
    ?>
</body>

</html>