<?php
    define("accessChecker", TRUE);

    session_start();
    $userID = $_SESSION["userID"];
    require "dbHelper.php";
    $dbHelper = new DBHelper($userID);

    $reco_categoryID  = $dbHelper->gen_reco_category();
    
    foreach ($reccom as $row) {         
        echo "<tr class='table-row' data-href='itemAuction.php?auctionID=" . $row["auctionID"] . "'>
                    <td>" . $row["itemName"] . "</td> 
                    <td>" . $row["description"] . "</td> 
                    <td>£" . number_format($row["startPrice"]/100, 2) . "</td>
                    <td>£" . number_format($row["reservePrice"]/100, 2) . "</td>
                    <td>" . $row["startDatetime"] . "</td>
                    <td>" . $row["endDatetime"] . "</td>
                    <td>£" . number_format($row["highestBid"]/100, 2) . "</td>
                </tr>";
    }

    
    


?>