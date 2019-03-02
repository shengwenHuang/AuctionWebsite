<?php
    define("accessChecker", TRUE);
    
    require "redirectIfNotLoggedIn.php";
    require "dbHelper.php";
    $dbHelper = new DBHelper(null);
    require "header.php";
?>
 
<!DOCTYPE html>
<html>

<head>
    <title>Search results</title>
    <link rel="stylesheet" href="css/table.css"/>
</head>

<body>
    <h1 style="margin-bottom: 20px">Search Results</h1>
    <?php
        $query = $_GET["query"]; 
        $catagory = $_GET["choices"];

        $raw_results = $dbHelper->fetch_search_results($query, $catagory);
        if ($raw_results) {
            // Get the highest bid for each item auction that was returned
            for ($i = 0; $i < count($raw_results); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($raw_results[$i]["auctionID"]);
                $raw_results[$i] = array_merge($raw_results[$i], $highestBidInfo);
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
            foreach ($raw_results as $row) {         
                echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
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
            unset($raw_results);
        } else {
            echo "No results found";
        }
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>