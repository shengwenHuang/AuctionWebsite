<?php include "header.php"?>
<?php include "database.php"?>

<!doctype html>
<html>

<body>
    <?php
        $userID = 9;
        $query = $pdo->prepare("SELECT DISTINCT(auctionID) FROM bids WHERE userID = ?");
        $query->execute(array($userID));
        $auctionArray = $query->fetchall();

        $rowData = array();
        if ($auctionArray) {
            $query = $pdo->prepare("SELECT i.itemName, i.description, MAX(b.bidAmount) AS yourBid, b.bidDatetime AS yourBiddt, a.endDatetime
                                   FROM items AS i, auctions AS a, bids AS b
                                   WHERE b.userID = ?
                                   AND b.auctionID = ?
                                   AND i.itemID = a.itemID
                                   AND a.auctionID = b.auctionID");

            foreach ($auctionArray as $auctionID) {
                $query->execute(array($userID, $auctionID["auctionID"]));
                $returnedRow = $query->fetch();
                if (empty($rowData)) {
                    $rowData = array($returnedRow);
                } else {
                    $rowData = array_push($rowData, $returnedRow);
                }
            }

            $query = $pdo->prepare("SELECT MAX(bidAmount) AS highestBid, bidDatetime AS highestBiddt FROM bids
                                    WHERE auctionID = ?");

            for ($i = 0; $i < count($auctionArray); $i++) {
                $query->execute(array($auctionArray[$i]["auctionID"]));
                $rowData[$i] = array_merge($rowData[$i], $query->fetch());
            }

            echo '<table border="0" cellspacing="2" cellpadding="2"> 
            <tr> 
                <td> <font face="Arial">Item Name</font> </td> 
                <td> <font face="Arial">Item Description</font> </td> 
                <td> <font face="Arial">Your Latest Bid</font> </td> 
                <td> <font face="Arial">Highest Bid</font> </td> 
                <td> <font face="Arial">End Date</font> </td> 
            </tr>';

            foreach ($rowData as $row) {         
                echo '<tr> 
                          <td>'.$row["itemName"].'</td> 
                          <td>'.$row["description"].'</td> 
                          <td>'.$row["yourBid"] . $row["yourBiddt"].'</td> 
                          <td>'.$row["highestBid"] . $row["highestBiddt"].'</td> 
                          <td>'.$row["endDatetime"].'</td> 
                      </tr>';
            }
            $result->free();
        } else {
            echo '<h1>No bids made<h1>';
        }   
    ?>
</body>

</html>
