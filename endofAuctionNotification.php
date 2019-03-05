<?php
    
    define("accessChecker", TRUE);
    require "dbHelper.php";
    $dbHelper = new DBHelper(1000);
    $dbHelper -> close_auctions();
?>