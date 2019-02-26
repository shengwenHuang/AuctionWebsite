<?php
  require "redirectIfNotLoggedIn.php";
  include "header.php";
  require "dbHelper.php";
  $userID = 1;
  $dbHelper = new DBHelper($userID);
?>

<!doctype html>
<html>

<body>
    <?php
        $result = $dbHelper -> fetch_your_listing($userID);
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
    ?>
</body>

</html>