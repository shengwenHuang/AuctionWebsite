<?php
    define("accessChecker", TRUE);
    
    require "redirectIfNotLoggedIn.php";
    require "dbHelper.php";
    $dbHelper = new DBHelper(null);
    require "header.php";
?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Search results</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php
    $query = $_GET['query']; 
    $catagory = $_GET['choices'];

    // minimum length of query 
    $min_length = 3;
     
    if(strlen($query) >= $min_length){ // if query length is more or equal minimum length then
         
        $raw_results = $dbHelper->search_results($query, $catagory);
        $printedResults = FALSE;
        if ($raw_results) { 
            foreach ($raw_results as $row) {
                echo "<p><h3>" . "<a href=itemAuction.php?auctionID=" . $row['auctionID'] . ">" .$row['itemName']. "</a>" . "</h3>".$row['endDateTime']."</p>";
                $printedResults = TRUE;
            }
             
        }
        if (!$printedResults) {//If there is no matching rows do following
            echo "No results";
        }
         
    }
    else{ // if query length is less than minimum
        echo "Minimum length is ".$min_length;
    }
?>
</body>
</html>