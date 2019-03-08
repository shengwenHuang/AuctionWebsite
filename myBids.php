<?php 
    define("accessChecker", TRUE);
    
    require "redirectIfNotLoggedIn.php";
    require "dbHelper.php";
    $userID = $_SESSION["userID"];
    $dbHelper = new DBHelper($userID);
    require "header.php";
?>

<!doctype html>
<html>

<head>
    <link rel="stylesheet" href="css/table.css">
</head>

<body>
    <?php
        // Add dropdown for sorting the order of displayed items
        $optionsValueArray = ["itemName", "yourBid", "yourBiddt", "endDatetime"];
        $optionsTextArray = ["Item Name", "Your Bids Amount", "Your Bids Datetime", "Auction End Datetime"];
        require "filterDropDown.php";

        // Retrieve a list of distinct auctionIDs that the current user has bid on
        $auctionArray = $dbHelper->fetch_future_auctions_by_user();

        if ($auctionArray) {
            // Initialise an empty array for the displayed table's row data
            $rowData = array();

            // Retrieve item, bid and auction details for the maximum bid made by the user in each given auction
            // and save each output row to the rowData array
            foreach ($auctionArray as $auction) {
                $returnedRow = $dbHelper->fetch_listing_by_user_auction($auction["auctionID"]);
                if ($returnedRow) {
                    // echo "hello";
                    // echo print_r($returnedRow);
                    array_push($rowData, $returnedRow);
                }
                
            }
            // echo print_r($rowData);
            // echo print_r($auctionArray);
            
            // Retrieve the current maximum bid for each given auction and append this to the corresponding row in
            // the rowData array
            for ($i = 0; $i < count($rowData); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($auctionArray[$i]["auctionID"]);
                $rowData[$i] = array_merge($rowData[$i], $highestBidInfo);
                $rowData[$i] = array_merge($rowData[$i], $auctionArray[$i]);
            }

            // Sort the resulting rows using a custom sorting function that sorts each row by the selected
            // key value
            $key = $_GET["orderBySelect"];
            usort($rowData, function($row1, $row2) use ($key)
            {
                if ($row1[$key] == $row2[$key]) {
                    return 0;
                } else {
                    return ($row1[$key] < $row2[$key]) ? -1 : 1;
                }
            });

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
                print("<p>" . $row["endDatetime"] . "</p>");
                
                    echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                          <td>" . $row["itemName"] . "</td> 
                          <td>" . $row["description"] . "</td> 
                          <td>£" . number_format($row["yourBid"]/100, 2) . " (" . $row["yourBiddt"] . ")</td> 
                          <td>£" . number_format($row["highestBid"]/100, 2) . " (" . $row["highestBiddt"] . ")</td> 
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