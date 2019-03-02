<?php
    define("accessChecker", TRUE);
    
    require "redirectIfNotLoggedIn.php";
    require "dbHelper.php";
    $dbHelper = new DBHelper(null);
    require "header.php";

    $userID = $_SESSION["userID"];
    if (isset($_GET["auctionID"])) {
        $auctionID = $_GET["auctionID"];
        $auction_details = $dbHelper->fetch_item_auction($auctionID);
        $item_categories = $dbHelper->fetch_item_categories($auction_details["itemID"]);
        $max_bid = $dbHelper->fetch_max_bid_for_auction($auctionID);
    } else {
        echo "<h1>Error: No auction ID was passed</h1>";
        die();
    }
?>

<!doctype html>
<html>

<!-- Auction page for an item -->
<body>   
    <!-- Item Name -->
    <h1><?php echo $auction_details["itemName"] ?></h1>

    <!-- Item and auction details -->
    <div style="margin-left: 25px; width: fit-content">
        <p>Description: <?php echo $auction_details["description"] ?></p>

        <h3>Item Categories:</h3>
        <ul>
        <?php
            if ($item_categories) {
                foreach ($item_categories as $category) {
                    echo "<li><p>" . $category["categoryName"]. "</p></li>";
                }
            } else {
                echo "<li><p>No categories</p></li>";
            }
            ?>
        </ul>

        <hr style="width: 100%; height: 2px; background-color: gray">

        <p>Start Price: £<?php echo number_format($auction_details["startPrice"]/100, 2) ?></p>
        <p>Reserve Price: £<?php echo number_format($auction_details["reservePrice"]/100, 2) ?></p>
        <p>Start Datetime: <?php echo str_replace(" ", ", ", $auction_details["startDatetime"]); ?></p>
        <p>End Datetime: <?php echo str_replace(" ", ", ", $auction_details["endDatetime"]); ?></p>
    </div>

    <!-- Current highest bid and total bids row, followed by a New Bids button that, when clicked,
     opens a dialog box to add a new bid to the auction -->
    <div style="display: flex; flex-flow: row; align-items: flex-start">
        <div style="display: flex; flex-flow: row; align-items: center">
            <h2 id="highest-bid" style="margin-right: 15px">Current Highest Bid: £<?php echo number_format($max_bid["highestBid"]/100, 2) ?></h2>
            <p id="total-bids" style="margin-right: 25px">(Total Number of Bids: <?php echo $auction_details["bidsNumber"] ?>)</p>
            <?php 
                $auctionDt = strtotime($auction_details["endDatetime"]);
                $currentDateObject = date("Y-m-d H:i:s");
                $currentDt = strtotime($currentDateObject);
                echo '<p>auction date:' . $auctionDt . '</p>';
                echo '<p>auction date:' . $auctionDt . '</p>';
                echo '<p>current date:' . $currentDt . '</p>';
                echo '<p>userID:' . $userID. '</p>';
                echo '<p>sellerID:' .$auction_details["sellerID"]. '</p>';
                // Only display the new bid button if the auction has not ended and the current user is not the seller
                if (($currentDt < $auctionDt) && ($userID != $auction_details["sellerID"])) {
                    echo "<button id='new-bid-btn' type='button' style='height: fit-content; margin-right: 25px'>New Bid</button>";
                }
            ?>
        </div>

        <!-- TODO: Hide new bid button if sellerID = current userID and if auction is in the past -->
        <div id="new-bid" style="display: none; border: 1px solid black; padding: 10px">
            <p style="margin-top: 0px">Your last highest bid: (php code)!!!!!!</p>
            <form action="process.php" method="POST">
                <label for="new-bid-amount">Bid Amount:</label>
                <div id="new-bid-amount" style="display: flex; align-items: center; margin-bottom: 25px">
                    <label style="margin-right: 10px">£</label>
                    <input name="bid-amount" type="number" min="0" step=".01" placeholder="0.00">
                </div>
                    <div style="text-align: end">
                        <!-- Empty input fields that are used to pass the auctionID, userID and starting auction amount in the POST request -->
                        <input name="bid-userID" style="display: none" value="<?php echo $userID ?>">
                        <input name="bid-auctionID" style="display: none" value="<?php echo $auctionID ?>">
                        <input name="bid-startAmount" style="display: none" value="<?php echo $auction_details["startPrice"]?>">

                        <button id="cancel-bid-btn" type="button" style="margin-right: 10px; background-color: red; color: white;">Cancel</button>
                        <button id="new-bid-btn" name="new-bid-made" type="submit">Make Bid</button>
                    </div>;
            </form>
        </div>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <h3 id="message" style="color: red"><?php echo $_GET['message']; ?></h3>
    <?php endif;?>

    <script>
        // When the user clicks on the New Bids button, open the dialog box,
        // hide the button and any messages that are visible
        var openBtn = document.getElementById("new-bid-btn"); 
        openBtn.onclick = function() {
            document.getElementById("new-bid").style.display = "block"; 
            openBtn.style.display = "none";
            
            var messageDiv = document.getElementById("message")
            if (messageDiv) {
                messageDiv.style.display = "none"; 
            }
        };

        // When the user clicks on the cancel button of the dialog box, close
        // it and show the New Bid button again
        var cancelBtn = document.getElementById("cancel-bid-btn");
        cancelBtn.onclick = function() {
            
            document.getElementById("new-bid").style.display = "none";
            openBtn.style.display = "block"           
        };
    </script>
</body>

</html>