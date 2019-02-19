<?php include "header.php";?>
<?php include "database.php";?>

<!doctype html>
<html>

<body>

    <?php
        /*Table with item, Number of bids (colour the row if at least one bid), auction end date*/
    ?>
    <?php
        $userID = 1;
        $query = $pdo -> prepare('SELECT i.itemName, i.description, COUNT(b.bidID) AS bidsNumber, a.endDatetime
                  FROM items as i, bids as b, auctions as a
                  WHERE i.itemID = a.itemID
                  AND a.auctionID = b.auctionID
                  AND i.sellerID = ?
                  GROUP BY i.itemName, i.description, a.endDatetime');
        $query -> execute(array($userID));
        $result = $query -> fetchall();
        if ($result) {
            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Bids Number</font> </td>
                <td> <font face="Arial">End Date</font> </td>
            </tr>';
            foreach ($result as $row) {
                echo '<tr>
                <td>'.$row["itemName"].'</td>
                <td>'.$row["description"].'</td>
                <td>'.$row["bidsNumber"].'</td>
                <td>'.$row["endDatetime"].'</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No listings</h1>';
        }
        
        $result->free();
    ?>
</body>

</html>