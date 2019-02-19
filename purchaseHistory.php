<?php include "header.php"?>
<?php
  require "dbHelper.php";
  $dbHelper = new DBHelper();
?>

<!doctype html>
<html>
<body>

    <?php
        /*- [ ]  Table with Item (href to the inactive bidding page), Amount paid, purchase date*/
    ?>
    <?php
        $userID = 2;
        /*$query = $pdo -> prepare('SELECT i.itemName, i.description, a.highestBid as amountPaid, a.endDatetime as purchaseDate, i.sellerID
                  FROM items as i, auctions as a, purchaseHistory as p
                  WHERE i.itemID = a.itemID
                  AND a.auctionID = p.auctionID
                  AND a.endDatetime < now()
                  AND p.buyerID = ?');
        $query -> execute(array($userID));
        $result = $query -> fetchall();*/
        $result = $dbHelper -> fetch_purchase_history($userID);
        if ($result) {
            echo '<table border="0" cellspacing="10" cellpadding="2">
            <tr>
                <td> <font face="Arial">Item Name</font> </td>
                <td> <font face="Arial">Item Description</font> </td>
                <td> <font face="Arial">Price</font> </td>
                <td> <font face="Arial">Purchase Date</font> </td>
                <td> <font face="Arial">Seller ID</font> </td>
            </tr>';
            foreach ($result as $row) {
                echo '<tr>
                <td>'.$row["itemName"].'</td>
                <td>'.$row["description"].'</td>
                <td>'.$row["amountPaid"].'</td>
                <td>'.$row["purchaseDate"].'</td>
                <td>'.$row["sellerID"].'</td>
                </tr>';
            }
        }
        else {
            echo '<h1>No purchase history</h1>';
        }
        
        $result->free();
    ?>
</body>

</html>
