<?php
class DBHelper {
    private $dbconnection;

    /**
     * Initialise the database connection on object creation
     * 
     * Try to create the PDO (PHP Data Object) database connection and catch any exceptions that occur. Use the
     * connection parameters to create a DSN (Data Source Name) and pass an options array to the PDO as its last
     * parameter to define certain behaviours. Show some basic details about the connection if it is made successfully. 
     */ 
    function __construct() {
        // Initialise database connection credentials.
        $dbhost = "localhost";
        $dbname   = "ebaylite";
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
            echo "Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode();
            die();
        }

        // If the database connection was created successfully, show some basic data about it in HTML script.
        echo "<p>Success: A working connection to MySQL was made!</p>";
        echo "<p>The database is: $dbname</p>";
        echo "<p>The host is: = $dbhost</p>";
        echo "<p>Host information: " . $this->dbconnection->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "</p>";
        echo "<p>The database username is: $dbuser</p>";
    }

    function fetch_user($username) {
        $query = $this->dbconnection->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        return $query->fetch();
    }

    function insert_user($username, $password, $email) {
        $query = $this->dbconnection->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        return $query->execute(array($username, $password, $email));
    }

    function fetch_purchase_history($userID) {
        $query = $this->dbconnection->prepare("SELECT i.itemName, i.description, a.highestBid as amountPaid, a.endDatetime as purchaseDate, i.sellerID
        FROM items as i, auctions as a, purchaseHistory as p
        WHERE i.itemID = a.itemID
        AND a.auctionID = p.auctionID
        AND a.endDatetime < now()
        AND p.buyerID = ?");
        $query->execute(array($userID));
        return $query->fetchall();
    }
    /**
     * Destroy the database connection when the object is no longer required
     * 
     * Kill the database connection by setting it equal to null when the object is no longer required by the
     * script that is using it. 
     */ 
    function __destruct() {
        $this->dbconnection = null;
    }
}
?>