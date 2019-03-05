<?php
    define("accessChecker", TRUE);

    session_start();
    $userID = $_SESSION["userID"];
    require "dbHelper.php";
    $dbHelper = new DBHelper($userID);

    $favorite = $dbHelper->fetch_auctionID_from_bids($userID);
    $favorite = array_count_values($favorite);
    print_r($favorite);
?>