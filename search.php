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
    <link rel="stylesheet" href="css/table.css" />
</head>

<body>
    <h1 style="margin-bottom: 20px">Search Results</h1>
    <?php
        $optionsValueArray = ["itemName", "startPrice", "reservePrice", "startDatetime", "endDatetime", "highestBid"];
        $optionsTextArray = ["Item Name", "Start Price", "Reserve Price", "Start Date/Time", "End Date/Time", "Highest Bid"];
        require "filterDropDown.php";

        $query = $_GET["query"]; 
        $catagory = $_GET["choices"];

        $raw_results = $dbHelper->fetch_search_results($query, $catagory);
        if ($raw_results) {
            // Get the highest bid for each item auction that was returned
            for ($i = 0; $i < count($raw_results); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($raw_results[$i]["auctionID"]);
                $raw_results[$i] = array_merge($raw_results[$i], $highestBidInfo);
            }

            // Sort the resulting rows using a custom sorting function that sorts each row by the selected
            // key value
            $key = $_GET["orderBySelect"];
            usort($raw_results, function($row1, $row2) use ($key)
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