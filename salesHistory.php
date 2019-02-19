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
        $result = $dbHelper -> fetch_sales_history($userID);
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