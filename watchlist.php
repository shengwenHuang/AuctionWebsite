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
        /*- [ ]  Table with Item (href), Highest bid, Auction end date
        - [ ]  Navigation to get back to home page*/
        $result = $dbHelper -> fetch_watch_list($userID);
        if ($result) {
            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Your Bid</font> </td>
                <td> <font face="Arial">Highest Bid</font> </td>
                <td> <font face="Arial">End Date</font> </td>
            </tr>';
            foreach ($result as $row) {
                echo '<tr>
                <td>'.$row["itemName"].'</td>
                <td>'.$row["description"].'</td>
                <td>'.$row["bidAmount"].'</td>
                <td>'.$row["highestBid"].'</td>
                <td>'.$row["endDatetime"].'</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No sale history</h1>';
        }
        
        $result->free();
    ?>
</body>

</html>
