<?php
// Initialise database connection credentials.
$dbhost = "localhost";
$dbname   = "ebaylite";
$dbuser = "root";
$dbpassword = "root";
$dbcharset = "utf8mb4";

// Setup the DSN (Data Source Name), which contains the information required to connect to the database,
// and the options for the PDO (PHP Data Object), which is a lean, consistent way to access databases.
$dsn = "mysql:host=$dbhost;dbname=$dbname;charset=$dbcharset";
$dboptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    ];

// Try to create the PDO and catch any errors with the connection.
try {
    $pdo = new PDO($dsn, $dbuser, $dbpassword, $dboptions);
} catch (PDOException $e) {
    echo "Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode();
}

// If the database connection was created successfully, show some basic data about it.
echo "<p>Success: A working connection to MySQL was made!</p>";
echo "<p>The database is: $dbname</p>";
echo "<p>The host is: = $dbhost</p>";
echo "<p>Host information: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "</p>";
echo "<p>The database username is: $dbuser</p>";
?>