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
    <form action="newListings.php" method="POST">
        <button style="font-size: 1.25em; padding: 10px; margin: 10px">Add New Listing</button>
    </form>

    <?php
        $optionsValueArray = ["itemName", "bidsNumber", "endDatetime"];
        $optionsTextArray = ["Item Name", "Number of Bids", "End Date/Time"];
        require "filterDropDown.php";

        $yourListings = $dbHelper -> fetch_your_listing($userID);
        if ($yourListings) {
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
                <th>Bids Number</th> 
                <th>End Date</th>
            </tr>";

            // Populate the table with the row data
            foreach ($yourListings as $row) {         
                echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                    <td>" . $row["itemName"] . "</td> 
                    <td>" . $row["description"] . "</td>
                    <td>" . $row["bidsNumber"] . "</td>
                    <td>" . $row["endDatetime"] . "</td>
                </tr>";
            }

            // Free up the memory used by the array
            unset($yourListings);
        }
        else {
            echo '<h1>No listings</h1>';
        }
    ?>

    <script type="text/javascript" src="js/table.js"></script>
</body>

</html>