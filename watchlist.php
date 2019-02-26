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
        $watchListInfo = $dbHelper -> fetch_watch_list($userID);
        if ($watchListInfo) {
            // Get the highest bid for each auction being watched
            for ($i = 0; $i < count($watchListInfo); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($watchListInfo[$i]["auctionID"]);
                $watchListInfo[$i] = array_merge($watchListInfo[$i], $highestBidInfo);
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
            </tr>";

            // Populate the table with the row data
            foreach ($watchListInfo as $row) {         
                echo "<tr  class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                    <td>" . $row["itemName"] . "</td> 
                    <td>" . $row["description"] . "</td> 
                    <td>£" . number_format($row["startPrice"]/100, 2) . "</td>
                    <td>£" . number_format($row["reservePrice"]/100, 2) . "</td>
                    <td>" . $row["startDatetime"] . "</td>
                    <td>" . $row["endDatetime"] . "</td>
                    <td>£" . number_format($row["highestBid"]/100, 2) . "</td>
                </tr>";
            }

            // Free up the memory used by the array
            unset($watchListInfo);
        }
        else {
            echo '<h1>No items are being watched</h1>';
        }
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>
