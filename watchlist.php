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
        $watchListInfo = $dbHelper -> fetch_watch_list($userID);
        if ($watchListInfo) {
            // Get the highest bid for each auction being watched
            for ($i = 0; $i < count($watchListInfo); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($watchListInfo[$i]["auctionID"]);
                $watchListInfo[$i] = array_merge($watchListInfo[$i], $highestBidInfo);
            }

            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Start Price</font> </td>
                <td> <font face="Arial">Reserve Price</font> </td>
                <td> <font face="Arial">Start Datetime</font> </td>
                <td> <font face="Arial">End Datetime</font> </td>
                <td> <font face="Arial">Highest Bid</font> </td>
            </tr>';

            foreach ($watchListInfo as $row) {
                echo '<tr>
                <td>' . $row["itemName"] . '</td>
                <td>' . $row["description"] . '</td>
                <td>£' . number_format($row["startPrice"]/100, 2) . '</td>
                <td>£' . number_format($row["reservePrice"]/100, 2) . '</td>
                <td>' . $row["startDatetime"] . '</td>
                <td>' . $row["endDatetime"] . '</td>
                <td>£' . number_format($row["highestBid"]/100, 2) . '</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No items are being watched</h1>';
        }
    ?>
</body>

</html>
