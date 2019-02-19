<?php include "redirectIfNotLoggedIn.php"?>
<?php include "header.php"?>
<?php include "database.php"?>


<!doctype html>
<html>

<body>
    <?php
        $userID = 9; // Need to change this to variable passed betwene pages

        // Retrieve a list of distinct auctionIDs that the current user has bid on
        $query = $pdo->prepare("SELECT DISTINCT(auctionID) FROM bids WHERE userID = ?");
        $query->execute(array($userID));
        $auctionArray = $query->fetchall();

        if ($auctionArray) {
            // Initialise an empty array for the displayed table's row data
            $rowData = array();

            // Create a query to retrieve item, bid and auction details for the maximum bid made by the user in a given auction
            $query = $pdo->prepare("SELECT i.itemName, i.description, MAX(b.bidAmount) AS yourBid, b.bidDatetime AS yourBiddt, a.endDatetime
                                   FROM items AS i, auctions AS a, bids AS b
                                   WHERE b.userID = ?
                                   AND b.auctionID = ?
                                   AND i.itemID = a.itemID
                                   AND a.auctionID = b.auctionID");

            // Run this query for each auction in the auctions list and save each output row to the rowData array
            foreach ($auctionArray as $auction) {
                $query->execute(array($userID, $auction["auctionID"]));
                $returnedRow = $query->fetch();
                array_push($rowData, $returnedRow);
            }

            // Create another query to retrieve the bid details of the highest overall bid for a given auction
            $query = $pdo->prepare("SELECT MAX(bidAmount) AS highestBid, bidDatetime AS highestBiddt FROM bids
                                    WHERE auctionID = ?");

            // Run this query for each auction in the auctions list and merge the output with the corresponding results already in the rowData array
            for ($i = 0; $i < count($auctionArray); $i++) {
                $query->execute(array($auctionArray[$i]["auctionID"]));
                $highestBidInfo = $query->fetch();
                $rowData[$i] = array_merge($rowData[$i], $highestBidInfo);
            }

            // HTML for the table to assign column headers
            echo '<table border="0" cellspacing="2" cellpadding="2"> 
            <tr> 
                <td> <font face="Arial">Item Name</font> </td> 
                <td> <font face="Arial">Item Description</font> </td> 
                <td> <font face="Arial">Your Latest Bid</font> </td> 
                <td> <font face="Arial">Highest Bid</font> </td> 
                <td> <font face="Arial">End Date</font> </td> 
            </tr>';

            // Populate the table with the row data
            foreach ($rowData as $row) {         
                echo '<tr> 
                          <td>'.$row["itemName"].'</td> 
                          <td>'.$row["description"].'</td> 
                          <td>'.$row["yourBid"] . " " . $row["yourBiddt"].'</td> 
                          <td>'.$row["highestBid"] . " " . $row["highestBiddt"].'</td> 
                          <td>'.$row["endDatetime"].'</td> 
                      </tr>';
            }

            // Free up the memory used by the array
            unset($rowData);
        } else {
            // If no auction bids were found for the current user, indicate this to them
            echo '<h1>No bids made<h1>';
        }   
    ?>
</body>

</html>