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
        // Retrieve a list of distinct auctionIDs that the current user has bid on
        $auctionArray = $dbHelper->fetch_auctions_by_user();

        if ($auctionArray) {
            // Initialise an empty array for the displayed table's row data
            $rowData = array();

            // Retrieve item, bid and auction details for the maximum bid made by the user in each given auction
            // and save each output row to the rowData array
            foreach ($auctionArray as $auction) {
                $returnedRow = $dbHelper->fetch_listing_by_user_auction($auction["auctionID"]);
                array_push($rowData, $returnedRow);
            }

            // Retrieve the current maximum bid for each given auction and append this to the corresponding row in
            // the rowData array
            for ($i = 0; $i < count($auctionArray); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($auctionArray[$i]["auctionID"]);
                $rowData[$i] = array_merge($rowData[$i], $highestBidInfo);
                $rowData[$i] = array_merge($rowData[$i], $auctionArray[$i]);
            }

            // HTML for the table to assign column headers
            echo "<table cellspacing='2' cellpadding='2'> 
            <tr> 
                <th>Item Name</th> 
                <th>Item Description</th> 
                <th>Your Latest Bid</th> 
                <th>Highest Bid</th> 
                <th>End Date</th> 
            </tr>";

            // Populate the table with the row data
            foreach ($rowData as $row) {         
                echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                          <td>" . $row["itemName"] . "</td> 
                          <td>" . $row["description"] . "</td> 
                          <td>£" . number_format($row["yourBid"], 2) . " (" . $row["yourBiddt"] . ")</td> 
                          <td>£" . number_format($row["highestBid"], 2) . " (" . $row["highestBiddt"] . ")</td> 
                          <td>" . $row["endDatetime"] . "</td>
                      </tr>";
            }

            // Free up the memory used by the array
            unset($rowData);
        } else {
            // If no auction bids were found for the current user, indicate this to them
            echo '<h1>No bids made</h1>';
        }   
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>