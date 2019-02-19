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