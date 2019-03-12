<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
if(!defined("accessChecker")) {
    die("Direct access not permitted");
}

class DBHelper
{
    private $userID;
    private $dbconnection;

    /**
     * Initialise the database connection on object creation
     *
     * Try to create the PDO (PHP Data Object) database connection and catch any exceptions that occur. Use the
     * connection parameters to create a DSN (Data Source Name) and pass an options array to the PDO as its last
     * parameter to define certain behaviours. Show some basic details about the connection if it is made successfully.
     */
    public function __construct($userID)
    {
        // Initialise user ID for the current session.
        $this->userID = $userID;

        // Initialise database connection credentials.
        $dbhost = "localhost";
        $dbname = "ebaylite";
        $dbcharset = "utf8mb4";
        $dbuser = "root";
        $dbpassword = "root";

        // Attempt to create the connection object.
        try {
            $this->dbconnection = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=$dbcharset", $dbuser, $dbpassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            echo "Error connecting to MySQL: " . $e->getMessage() . (int) $e->getCode();
            die();
        }

        // If the database connection was created successfully, show some basic data about it in HTML script.
        // echo "<p>Success: A working connection to MySQL was made!</p>";
        // echo "<p>The database is: $dbname</p>";
        // echo "<p>The host is: = $dbhost</p>";
        // echo "<p>Host information: " . $this->dbconnection->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "</p>";
        // echo "<p>The database username is: $dbuser</p>";
    }

    public function fetch_user($username)
    {
        $query = $this->dbconnection->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        return $query->fetch();
    }

    public function insert_user($username, $password, $email)
    {
        $query = $this->dbconnection->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        return $query->execute(array($username, $password, $email));
    }

    // public function fetch_auctions_by_user()
    // {
    //     if (isset($this->userID)) {
    //         $query = $this->dbconnection->prepare("SELECT DISTINCT(auctionID) FROM bids WHERE userID = ?");
    //         $query->execute(array($this->userID));
    //         return $query->fetchall();
    //     }
    // }

    public function fetch_future_auctions_by_user()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare("SELECT DISTINCT(bids.auctionID) 
                                                   FROM bids, auctions
                                                   WHERE userID = ?
                                                   AND bids.auctionID = auctions.auctionID
                                                   AND auctions.endDateTime > now()");
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }


    public function fetch_listing_by_user_auction($auctionID)
    {
        if (isset($this->userID)) {
            // Create a query to retrieve item, bid and auction details for the maximum bid made by the user in a given auction
            // old query
            // "SELECT i.itemName, i.description, MAX(b.bidAmount) AS yourBid, b.bidDatetime AS yourBiddt, a.endDatetime
                // FROM items AS i, auctions AS a, bids AS b
                // WHERE b.userID = ?
                // AND b.auctionID = ?
                // AND i.itemID = a.itemID
                // AND a.auctionID = b.auctionID
                // GROUP BY itemName, description, yourBiddt, endDatetime"
            
            
            $query = $this->dbconnection->prepare(
                
                "SELECT i.itemName, i.description, b.bidAmount AS yourBid, b.bidDatetime AS yourBiddt, a.endDatetime
                FROM items AS i, auctions AS a, bids AS b
                WHERE b.userID = ?
                AND b.auctionID = ?
                AND i.itemID = a.itemID
                AND a.auctionID = b.auctionID
                ORDER BY yourBid DESC"
            );
            $query->execute(array($this->userID, $auctionID));
            return $query->fetch();
        }
    }

    public function fetch_max_bid_for_auction($auctionID)
    {
        // Create a query to retrieve the bid details of the highest overall bid for a given auction
        $query = $this->dbconnection->prepare(
            "SELECT bidAmount AS highestBid, bidDatetime AS highestBiddt FROM bids WHERE auctionID = ?
            ORDER BY highestBid DESC
            LIMIT 1"
        );
        $query->execute(array($auctionID));
        $rows = $query->fetch();
        if (!isset($rows["highestBid"])) {
            $rows["highestBid"] = 0;
        }
        $rows["highestBid"] = $rows["highestBid"];
        return $rows;
    }

    public function fetch_purchase_history()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT p.auctionID, i.itemName, i.description, a.endDatetime as purchaseDate
                FROM items as i, auctions as a, purchaseHistory as p
                WHERE p.auctionID = a.auctionID
                AND a.itemID = i.itemID
                AND p.buyerID = ?"
            );
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }

    // OLD FUNCTION
    // public function fetch_purchase_history()
    // {
    //     if (isset($this->userID)) {
    //         $query = $this->dbconnection->prepare(
    //             "SELECT b.auctionID, i.itemName, i.description, a.endDatetime as purchaseDate, i.sellerID, b.bidAmount
    //             FROM items as i, auctions as a, bids as b
    //             WHERE b.auctionID = a.auctionID
    //             AND a.itemID = i.itemID
    //             AND a.endDatetime < now()
    //             AND b.userID = ?"
    //         );

    //         $query->execute(array($this->userID));
    //         $result = $query->fetchall();

    //         $toReturn = array();
    //         foreach ($result as $row) {
    //             $res = ($this->fetch_max_bid_for_auction($row['auctionID']));
    //             if ($res['highestBid'] == $row['bidAmount']) {
    //                 array_push($toReturn, $row);
    //             }
    //         }
    //         return $toReturn;
    //     }
    // }

    public function fetch_sales_history()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT p.auctionID, i.itemName, i.description, a.endDatetime as saleDate
                FROM items as i, auctions as a, purchaseHistory as p
                WHERE p.auctionID = a.auctionID
                AND a.itemID = i.itemID
                AND i.sellerID = ?"
            );
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }

    public function fetch_your_listing()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT a.auctionID, i.itemName, i.description, a.endDatetime
                FROM items as i, auctions as a
                WHERE i.itemID = a.itemID
                AND a.endDatetime > now()
                AND i.sellerID = ?
                GROUP BY a.auctionID, i.itemName, i.description, a.endDatetime"
            );
            $query->execute(array($this->userID));
            $rows = $query->fetchall();
            $query_get_bidsNumber = $this->dbconnection->prepare(
                "SELECT COUNT(b.bidID) AS bidsNumber
                FROM bids as b
                WHERE auctionID = ?"
            );
            for ($i = 0; $i < sizeof($rows); $i++) {
                $query_get_bidsNumber->execute(array($rows[$i]["auctionID"]));
                $bidsNumberRow = $query_get_bidsNumber->fetch();
                if (!isset($bidsNumberRow["bidsNumber"])) {
                    $bidsNumberRow["bidsNumber"] = 0;
                }
                $rows[$i]["bidsNumber"] = $bidsNumberRow["bidsNumber"];
            }
            
            // $bidsNumberRow = $query_get_bidsNumber->fetchall();
            // for ($i = 0, $size = count($bidsNumberRow); $i < $size; ++$i) {
            //     $row[$i]["bidsNumber"] = $bidsNumberRow[$i]["bidsNumber"];
            // }
            // foreach ($row as $r) {
            //     if (!array_key_exists("bidsNumber", $r)) {
            //         $r["bidsNumber"] = 0;
            //     }
            // }
            
            return $rows;
            // $query = $this->dbconnection->prepare(
            //     "SELECT a.auctionID, i.itemName, i.description, COUNT(b.bidID) AS bidsNumber, a.endDatetime
            //     FROM items as i, bids as b, auctions as a
            //     WHERE i.itemID = a.itemID
            //     AND a.auctionID = b.auctionID
            //     AND a.endDatetime > now()
            //     AND i.sellerID = ?
            //     GROUP BY a.auctionID, i.itemName, i.description, a.endDatetime"
            // );
            // $query->execute(array($this->userID));
            // if ($query->rowCount() == 0) {
            //     $query = $this->dbconnection->prepare(
            //         "SELECT a.auctionID, i.itemName, i.description, a.endDatetime
            //         FROM items as i, auctions as a
            //         WHERE i.itemID = a.itemID
            //         AND a.endDatetime > now()
            //         AND i.sellerID = ?
            //         GROUP BY a.auctionID, i.itemName, i.description, a.endDatetime"
            //     );
            //     $query->execute(array(($this->userID)));
            //     $row = $query->fetchall();
            //     $row["bidsNumber"] = 0;
            //     return $row;
            // }
            // return $query->fetchall();
        }
    }

    public function fetch_user_id_from_username($username){
        $query = $this->dbconnection->prepare(
            "SELECT userID
            FROM users
            WHERE username = ?"
        );
        $query->execute(array($username));
        $row = $query->fetch();
        return $row["userID"];
    }
    public function fetch_watch_list()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT a.auctionID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime
                FROM watchList as wl, items as i, auctions as a
                WHERE wl.auctionID = a.auctionID
                AND a.itemID = i.itemID
                AND a.endDatetime > now()
                AND wl.userID = ?"
            );
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }

    public function fetch_item_auction($auctionID)
    {
        $query = $this->dbconnection->prepare(
            "SELECT i.itemID, i.sellerID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime, COUNT(b.bidID) AS bidsNumber
            FROM items as i, bids as b, auctions as a
            WHERE i.itemID = a.itemID
            AND a.auctionID = b.auctionID
            AND a.auctionID = ?
            GROUP BY i.itemID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime"
        );
        $query->execute(array($auctionID));
        if ($query->rowCount() == 0) {
            $query = $this->dbconnection->prepare(
                "SELECT i.itemID, i.sellerID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime
                FROM items as i, auctions as a
                WHERE i.itemID = a.itemID
                AND a.auctionID = ?"
            );
            $query->execute(array($auctionID));
            $row = $query->fetch();
            $row["bidsNumber"] = 0;
            return $row;
        }
        return $query->fetch();    
    }

    public function fetch_item_categories($itemID)
    {
        $query = $this->dbconnection->prepare(
            "SELECT c.categoryName FROM categories as c, itemCategories as ic
             WHERE c.categoryID = ic.categoryID
             AND ic.itemID = ?"
        );
        $query->execute(array($itemID));
        return $query->fetchall();
    }

    public function insert_new_bid($userID, $auctionID, $bidAmount, $bidDatetime)
    {
        $query = $this->dbconnection->prepare("INSERT INTO bids (userID, auctionID, bidAmount, bidDatetime) VALUES (?, ?, ?, ?)");
        return $query->execute(array($userID, $auctionID, $bidAmount, $bidDatetime));
    }

    public function get_catagories() {
        $query = $this->dbconnection->prepare(
            "SELECT categoryName
            FROM categories"
        );
        $query->execute();
        return $query->fetchall();
    }
    
    public function fetch_user_email_from_username($username){
        $query = $this->dbconnection->prepare(
            "SELECT email FROM users WHERE username = ?");
        $query->execute(array($username));
        $row = $query->fetch();
        return $row["email"];
    }
    
    public  function update_email($email, $username){
        $query = $this->dbconnection->prepare("UPDATE users SET email=? WHERE username = ?");
        $query->execute(array($email, $username));
    }
    
    public  function change_password($password, $username){
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = $this->dbconnection->prepare("UPDATE users SET password=? WHERE username = ?");
        $query->execute(array($password, $username));
    }
    
    public function fetch_itemid_from_items($itemName,$sellerID){
        $query = $this->dbconnection->prepare(
            "SELECT itemID FROM items WHERE itemName = ? AND sellerID = ?");
        $query->execute(array($itemName,$sellerID));
        $row = $query->fetch();
        return $row["itemID"];
    }

    public function fetch_categoryid_from_category($item_category){
        $query = $this->dbconnection->prepare(
            "SELECT categoryID FROM categories WHERE categoryName = ?");
        $query->execute(array($item_category));
        $row = $query->fetch();
        return $row["categoryID"];
    }
    
    public function fetch_all_items_from_categoris($categoryID){
        $query = $this->dbconnection->prepare(
            "SELECT itemID FROM ItemCategories WHERE categoryID = ?");
        $query->execute(array($categoryID));
        $row = $query->fetch();
        return $row["itemID"];
    }
    
    public function fetch_popular_auctionID($categoryID){
        $query = $this->dbconnection->prepare(
            "SELECT bids.auctionID, bids.bidAmount, items.itemName
            FROM bids, auctions, items, itemCategories
            WHERE bids.auctionID = auctions.auctionID
            AND auctions.itemID = items.itemID
            AND items.itemID = itemCategories.itemID
            AND itemCategories.categoryID = ?");
        $query->execute(array($categoryID));
        $row = $query->fetch();
        $array =  $row["itemName"];
        $values = array_count_values($array);
        arsort($values);
        return array_slice(array_keys($values), 0, 5, true);
    }
    
    public function insert_item($itemName, $sellerID, $description)
    {
        $query = $this->dbconnection->prepare("INSERT INTO items (itemName, sellerID, description) VALUES (?, ?, ?)");
        $result = $query->execute(array($itemName, $sellerID, $description)); // true or false
        $newItemID = $this->dbconnection->lastInsertId();
        if ($result) { // if the insert succeed, then return the itmeID
            return $newItemID;
        }
        return; // if the insert failed, return null and insert_auction will throw an error
    }

    public function insert_item_category($newItemID, $categoryID)
    {
        $query = $this->dbconnection->prepare("INSERT INTO itemCategories (itemID, categoryID) VALUES (?, ?)");
        return $query->execute(array($newItemID, $categoryID));
    }
    
    public function insert_auction($itemID, $start_price, $reserve_price, $startDatetime, $endDatetime)
    {
        $query = $this->dbconnection->prepare("INSERT INTO auctions (itemID,startPrice,reservePrice,startDatetime,endDatetime) VALUES ( ?, ?, ?, ?, ?)");
        return $query->execute(array($itemID, $start_price * 100, $reserve_price * 100, $startDatetime, $endDatetime));
    }

    public function fetch_search_results ($searchQuery, $category){
        // Changes characters used in html to their equivalents, for example: < to &gt
        // $searchQuery = htmlspecialchars($searchQuery);
        // $searchQuery = "%{$searchQuery}%";

        // If there was no category selected, just seach by query string
        if ($category == "Category") {
            $query = $this->dbconnection->prepare(
                "SELECT a.auctionID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime
                FROM items as i, auctions as a
                WHERE a.itemID = i.itemID
                AND a.endDatetime > now()
                AND itemName LIKE CONCAT('%',?,'%')"
            );
            $query->execute(array($searchQuery));
        }
        else {
            // If a category was selected, search by query string and category name
            $query = $this->dbconnection->prepare(
                "SELECT a.auctionID, i.itemName, i.description, a.startPrice, a.reservePrice, a.startDatetime, a.endDatetime
                FROM items as i, auctions as a, itemCategories as ic, categories as c
                WHERE a.itemID = i.itemID
                AND i.itemID = ic.itemID
                AND ic.itemID = c.categoryID
                AND a.endDatetime > now()
                AND itemName LIKE CONCAT('%',?,'%')
                AND c.categoryName = ?"
            );
            $query->execute(array($searchQuery, $category));
        }
        
        return $query->fetchall();
    }

    // function to send email to highest current bidder. call before inserting a new highest bidder to inform the previous highest
    // bidder that they have been outbid. Or pass in bool = true at the end of the auction to email the highest bidder and inform them that they've
    // won the auction.
    public function sendEmailToBidder($auctionID, $bool = false) {
        $query = $this->dbconnection->prepare(
            "SELECT username, email, userID
            FROM users
            WHERE userID = (SELECT userID
            FROM bids, auctions
            WHERE auctionID = ?
            AND bids.auctionID = auction.auctionID
            AND bids.bidAmount >= auctions.reservePrice
            ORDER BY bidAmount DESC
            LIMIT 1)"
        );
        
        $query->execute(array($auctionID));
        
        if ($query->rowCount() == 0) {
            return;
        }
        $row = $query->fetch();
        $userID = $row['userID'];
        $username = $row['username'];
        $email = $row['email'];
        // use PHPMailer\PHPMailer\PHPMailer;
        // require '../vendor/autoload.php';
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 2;
        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "databasecoursework@gmail.com";
        //Password to use for SMTP authentication
        $mail->Password = "pFKdcJ4LxMNN8q7n";
        //Set who the message is to be sent from
        $mail->setFrom('databasecoursework@gmail.com', 'EbayLite');
        //Set an alternative reply-to address
        $mail->addReplyTo('databasecoursework@gmail.com', 'EbayLite');
        //Set who the message is to be sent to
        $mail->addAddress($email, $username);
        //Set the subject line
        if ($bool) {
            $mail->Subject = 'You\'ve won the auction!';
            $newQuery = $this->dbconnection->prepare(
                "INSERT into purchaseHistory buyerID, auctionID VALUES (?,?)"
            );
            $newQuery->execute(array($userID,$auctionID));

        }
        else {
            $mail->Subject = 'You have been outbid!';
        }
        
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        if ($bool) {
            $mail->msgHTML('<p>Hi ' . $username . '</p><p>You won auction ID: ' . $auctionID . '</p><p> Please go to http://localhost:8888/itemAuction.php?auctionID=' . $auctionID . ' to see your purchase</p>');
        }
        else {
            $mail->msgHTML('<p>Hi ' . $username . '</p><p>You have been outbid on auction ID: ' . $auctionID . '</p><p> Please go to http://localhost:8888/itemAuction.php?auctionID=' . $auctionID . ' if you would like to raise your bid</p>');
        }
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }

        $query = $this->dbconnection->prepare(
            "INSERT INTO notifications (userID, auctionID, datetimeAdded) VALUES(?,?,NOW())"
        );
        $query->execute(array($userID, $auctionID));
    }    

    public function close_auctions()
    {
        $query = $this->dbconnection->prepare(
            "SELECT a.auctionID FROM auctions as a WHERE a.endDatetime < now() AND a.endDatetime > addtime(now(), '-01:00')"
        );
        $query->execute();
        

        if ($query->rowCount() > 0) {
            $closedAuctions = $query->fetchall();
            foreach ($closedAuctions as $auction) {
                $auctionID = $auction["auctionID"];
                $this->sendEmailToBidder($auctionID, true);
                $this->sendEmailtoSellerAtAuctionEnd($auctionID);
            }
        }
    }

    public function sendEmailToSellerAtAuctionEnd ($auctionID) {
        $query = $this->dbconnection->prepare(
            "SELECT username, email, users.userID
            FROM users, items, auctions, bids
            WHERE auctions.auctionID = ?
            AND auctions.itemID = items.itemID
            AND items.sellerID = users.userID
            AND auctions.auctionID = bids.auctionID
            AND bids.bidAmount >= auctions.reservePrice
            LIMIT 1"
        );
        $query->execute(array($auctionID));
        
       
        if ($query->rowCount() > 0) {
            $row = $query->fetch();
            $userID = $row['userID'];
            $username = $row['username'];
            $email = $row['email'];
            $subj = 'Congratulations, your item Sold!';
            $msg = '<p>Hi ' . $username . '</p><p>Your item sold! Auction ID: ' . $auctionID . '</p><p> Please go to http://localhost:8888/itemAuction.php?auctionID=' . $auctionID . ' to see your sale</p>';
        }
        else{
            $query = $this->dbconnection->prepare(
                "SELECT username, email, users.userID
                FROM users, items, auctions, bids
                WHERE auctions.auctionID = ?
                AND auctions.itemID = items.itemID
                AND items.sellerID = users.userID
                LIMIT 1"
            );
            
            $query->execute(array($auctionID));
            $row = $query->fetch();
            $userID = $row['userID'];
            $username = $row['username'];
            $email = $row['email'];
            $subj = 'Commiserations, your item didn\'t sell';
            $msg = '<p>Hi ' . $username . '</p><p>Your item didn\'t sell. Auction ID: ' . $auctionID . '</p><p> Please go to http://localhost:8888/itemAuction.php?auctionID=' . $auctionID . ' to see your item</p>';
        }
     
        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 2;
        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "databasecoursework@gmail.com";
        //Password to use for SMTP authentication
        $mail->Password = "pFKdcJ4LxMNN8q7n";
        //Set who the message is to be sent from
        $mail->setFrom('databasecoursework@gmail.com', 'EbayLite');
        //Set an alternative reply-to address
        $mail->addReplyTo('databasecoursework@gmail.com', 'EbayLite');
        //Set who the message is to be sent to
        $mail->addAddress($email, $username);
        //Set the subject line
        $mail->Subject = $subj;
        
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        $mail->msgHTML($msg);
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }

        $query = $this->dbconnection->prepare(
            "INSERT INTO notifications (userID, auctionID, datetimeAdded) VALUES(?,?,NOW())"
        );
        $query->execute(array($userID, $auctionID));
    }

    public function fetch_recommendations () {
        $query = $this->dbconnection->prepare(
            "SELECT itemName, description, startPrice, reservePrice, startDatetime, endDatetime, dateOfRecommendation, auctionID
            FROM recommendations, items, auctions
            WHERE userID = ?
            AND itemRecommendation = items.itemID
            AND items.itemID = auctions.itemID
            AND auctions.endDatetime > now()
            ORDER by dateOfRecommendation DESC
            LIMIT 5"
        );
        $query->execute(array($this->userID));
        return $query->fetchall();
    }

    public function gen_reco_item($userID) {
        $query = $this->dbconnection->prepare(
            "SELECT itemcategories.categoryID , COUNT(bids.bidID) AS numberOfBids
            FROM itemcategories, bids, auctions
            WHERE auctions.auctionID = bids.auctionID
            AND auctions.itemID = itemcategories.itemID
            AND bids.userID = ?
            GROUP BY itemcategories.CategoryID
            ORDER BY 'numberOfBids' DESC");
        $query->execute(array($userID));
        
        if ($query->rowCount() == 0) {
            return;
        }
        $rows = $query->fetchall();
        
        // Filter the returned list of rows so that it only contains the ones with the
        // highest number of bids
        $reco_categoryID = $this->filter_highest_value($rows, "categoryID");
        // Return a list of category IDs by number of bids (From the database)
        $query = $this->dbconnection->prepare(
            'SELECT i.itemID, COUNT(b.bidID) as numberOfBids
            FROM items as i, bids as b, auctions as a, itemCategories as ic
            WHERE b.auctionID = a.auctionID
            AND a.itemID = ic.itemID
            AND i.itemID = ic.itemID
            AND ic.categoryID = ?
            AND a.endDatetime > now()
            GROUP BY i.itemID
            ORDER BY numberOfBids DESC');
        $query->execute(array($reco_categoryID));
        if ($query->rowCount() == 0) {
            return;
        }
        $rows = $query->fetchall();
        
        $reco_itemID = $this->filter_highest_value($rows, "itemID");
        
        try {
            $query = $this->dbconnection->prepare('INSERT INTO recommendations (userID, itemRecommendation, dateOfRecommendation) values (?, ?, ?)');
            $query->execute(array($userID, $reco_itemID, date("Y-m-d H:i:s")));
        } catch (PDOException $e) {
            return;
        }

    }

    private function filter_highest_value ($rows, $IDName) {
        $highestResults = array();
        foreach ($rows as $row) {
            if (sizeof($highestResults) == 0) {
                array_push($highestResults, $row);
            } else {
                $bidNumber = $row["numberOfBids"];
                $currentHighest = $highestResults[0]["numberOfBids"];

                if ($bidNumber > $currentHighest) {
                    $highestResults = array();
                    array_push($highestResults, $row);
                } else if ($bidNumber == $currentHighest) {
                    array_push($highestResults, $row);
                }
            }
        }
        // If the final array only contains one value, return its categoryID, otherwise
        // generate a random index for the array and then return the categoryID for that
        // random row
        $arraySize = sizeof($highestResults);
        if ($arraySize > 1) {
            $randomIndex = rand(0, $arraySize-1);
            return $highestResults[$randomIndex][$IDName];
        } else {
            return $highestResults[0][$IDName];
        }
    }

    public function check_watch_item($auctionID)
    {
        $query = $this->dbconnection->prepare(
            "SELECT watchID FROM watchList WHERE userID = ? AND auctionID = ?"
        );
        $query->execute(array($this->userID, $auctionID));
        return $query->fetch();
    }

    public function add_watch_item($auctionID)
    {
        $query = $this->dbconnection->prepare(
            "INSERT INTO watchList (userID, auctionID) VALUES (?, ?)"
        );
        return $query->execute(array($this->userID, $auctionID));
    }

    public function remove_watch_item($auctionID)
    {
        $query = $this->dbconnection->prepare(
            "DELETE FROM watchlist WHERE userID = ? AND auctionID = ?"
        );
        return $query->execute(array($this->userID, $auctionID));
    }

    /**
     * Destroy the database connection when the object is no longer required
     *
     * Kill the database connection by setting it equal to null when the object is no longer required by the
     * script that is using it.
     */
    public function __destruct()
    {
        $this->dbconnection = null;
    }
}