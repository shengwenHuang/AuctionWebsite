<?php include "header.php"?>
<?php include "database.php";?>

<!doctype html>
<html>
<body>

    <?php
        /*Table with item, Sale price, buyerID, auction end date*/
    ?>
    <?php
        $userID = 2;
        $query = $pdo -> prepare('SELECT i.itemName, i.description, a.highestBid, a.endDatetime, p.buyerID
                  FROM items as i, auctions as a, purchaseHistory as p
                  WHERE i.itemID = a.itemID
                  AND a.auctionID = p.auctionID
                  AND a.endDatetime < now()
                  AND i.sellerID = ?');
        $query -> execute(array($userID));
        $result = $query -> fetchall();
        if ($result) {
            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Sale Price</font> </td>
                <td> <font face="Arial">End Date</font> </td>
                <td> <font face="Arial">Buyer ID</font> </td>
            </tr>';
            foreach ($result as $row) {
                echo '<tr>
                <td>'.$row["itemName"].'</td>
                <td>'.$row["description"].'</td>
                <td>'.$row["highestBid"].'</td>
                <td>'.$row["endDatetime"].'</td>
                <td>'.$row["buyerID"].'</td>
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