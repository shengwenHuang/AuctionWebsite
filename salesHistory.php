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
        $optionsValueArray = ["itemName", "saleDate", "highestBid"];
        $optionsTextArray = ["Item Name", "Sale Date/Time", "Sale Price"];
        require "filterDropDown.php";
    
        $salesHistory = $dbHelper -> fetch_sales_history($userID);
        if ($salesHistory) {
            // Get the highest bid for each auction that was won as the final price that was paid
            for ($i = 0; $i < count($salesHistory); $i++) {
                $highestBidInfo = $dbHelper->fetch_max_bid_for_auction($salesHistory[$i]["auctionID"]);
                $salesHistory[$i] = array_merge($salesHistory[$i], $highestBidInfo);
            }

            // Sort the resulting rows using a custom sorting function that sorts each row by the selected
            // key value
            $key = $_GET["orderBySelect"];
            usort($salesHistory, function($row1, $row2) use ($key)
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
                <th>Sale Datetime</th> 
                <th>Sale Price</th>
            </tr>";

            // Populate the table with the row data
            foreach ($salesHistory as $row) {         
                echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                          <td>" . $row["itemName"] . "</td> 
                          <td>" . $row["description"] . "</td> 
                          <td>" . $row["saleDate"] . "</td>
                          <td>£" . number_format($row["highestBid"]/100, 2) . "</td>
                      </tr>";
            }

            // Free up the memory used by the array
            unset($salesHistory);
        }
        else {
            echo '<h1>No sales history</h1>';
        }
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>