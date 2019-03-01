<?php
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
        echo "<p>Success: A working connection to MySQL was made!</p>";
        echo "<p>The database is: $dbname</p>";
        echo "<p>The host is: = $dbhost</p>";
        echo "<p>Host information: " . $this->dbconnection->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "</p>";
        echo "<p>The database username is: $dbuser</p>";
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

    public function fetch_auctions_by_user()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare("SELECT DISTINCT(auctionID) FROM bids WHERE userID = ?");
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }

    public function fetch_listing_by_user_auction($auctionID)
    {
        if (isset($this->userID)) {
            // Create a query to retrieve item, bid and auction details for the maximum bid made by the user in a given auction
            $query = $this->dbconnection->prepare(
                "SELECT i.itemName, i.description, MAX(b.bidAmount) AS yourBid, b.bidDatetime AS yourBiddt, a.endDatetime
                FROM items AS i, auctions AS a, bids AS b
                WHERE b.userID = ?
                AND b.auctionID = ?
                AND i.itemID = a.itemID
                AND a.auctionID = b.auctionID
                GROUP BY itemName, description, yourBiddt, endDatetime"
            );
            $query->execute(array($this->userID, $auctionID));
            return $query->fetch();
        }
    }

    public function fetch_max_bid_for_auction($auctionID)
    {
        // Create a query to retrieve the bid details of the highest overall bid for a given auction
        $query = $this->dbconnection->prepare("SELECT MAX(bidAmount) AS highestBid, bidDatetime AS highestBiddt FROM bids WHERE auctionID = ? GROUP BY highestBiddt");
        $query->execute(array($auctionID));
        return $query->fetch();
    }

    // TODO: WE NEED TO GET THE PURCHASE HISTORY TABLE FROM THE MAX BIDS FOR EACH AUCTION??
    public function fetch_purchase_history()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT p.auctionID, i.itemName, i.description, a.endDatetime as purchaseDate, i.sellerID
                FROM items as i, auctions as a, purchaseHistory as p
                WHERE p.auctionID = a.auctionID
                AND a.itemID = i.itemID
                AND a.endDatetime < now()
                AND p.buyerID = ?"
            );
            $query->execute(array($this->userID));
            return $query->fetchall();
        }
    }

    public function fetch_sales_history()
    {
        if (isset($this->userID)) {
            $query = $this->dbconnection->prepare(
                "SELECT p.auctionID, i.itemName, i.description, a.endDatetime as saleDate, p.buyerID
                FROM items as i, auctions as a, purchaseHistory as p
                WHERE p.auctionID = a.auctionID
                AND a.itemID = i.itemID
                AND a.endDatetime < now()
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
                "SELECT a.auctionID, i.itemName, i.description, COUNT(b.bidID) AS bidsNumber, a.endDatetime
                FROM items as i, bids as b, auctions as a
                WHERE i.itemID = a.itemID
                AND a.auctionID = b.auctionID
                AND a.endDatetime > now()
                AND i.sellerID = ?
                GROUP BY a.auctionID, i.itemName, i.description, a.endDatetime"
            );
            $query->execute(array($this->userID));
            return $query->fetchall();
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
    
    public function insert_item($itemName, $sellerID, $description)
    {
        $query = $this->dbconnection->prepare("INSERT INTO items (itemName, sellerID, description) VALUES ( ?, ?, ?)");
        return $query->execute(array($itemName,$sellerID, $description));
    }
    
    public function insert_auction($itemID, $start_price, $reserve_price, $startDatetime, $endDatetime)
    {
        $query = $this->dbconnection->prepare("INSERT INTO auctions (itemID,startPrice,reservePrice,startDatetime,endDatetime) VALUES ( ?, ?, ?, ?, ?)");
        return $query->execute(array($itemID, $start_price * 100, $reserve_price * 100, $startDatetime, $endDatetime));
    }

    public function search_results ($searchQuery, $category){
        // changes characters used in html to their equivalents, for example: < to &gt;
        $searchQuery = htmlspecialchars($searchQuery);

        // $searchQuery = mysql_real_escape_string($searchQuery);
        $searchQuery = "%{$searchQuery}%";
        if ($category == "Category") {
            $query = $this->dbconnection->prepare(
                "SELECT itemName, endDateTime, auctionID
                FROM items, auctions
                WHERE itemName LIKE CONCAT('%',?,'%')
                AND items.itemID = auctions.itemID
                AND endDateTime >=  NOW()
                ORDER BY endDateTime DESC"
            );
            $query->execute(array($searchQuery));
        }
        else {
            $query = $this->dbconnection->prepare(
                "SELECT distinct itemName, endDateTime, auctionID
                FROM items, auctions, itemCategories, categories
                WHERE itemName LIKE CONCAT('%',?,'%')
                AND items.itemID = auctions.itemID
                AND items.itemID = itemCategories.itemID
                AND itemCategories.itemID = categories.categoryID
                and categories.categoryName = ?
                AND endDateTime >=  NOW()
                ORDER BY endDateTime DESC"
            );
            $query->execute(array($searchQuery, $category));
        }
        
        // $query->bind_param("s", $searchQuery);
        // return $query;
        // $query->execute();
        
        return $query->fetchall();
        // return $query;
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