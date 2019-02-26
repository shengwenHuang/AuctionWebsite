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
        $yourListings = $dbHelper -> fetch_your_listing($userID);
        if ($yourListings) {
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