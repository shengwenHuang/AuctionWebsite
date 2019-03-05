<?php
    define("accessChecker", TRUE);

    session_start();
    $userID = $_SESSION["userID"];
    require "dbHelper.php";
    $dbHelper = new DBHelper($userID);

    $rows = $dbHelper->fetch_auctionID_from_bids($userID);
     // Return a list of category IDs by number of bids (From the database)

        // Filter the returned list of rows so that it only contains the ones with the
        // highest number of bids
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
            return $highestResults[$randomIndex]["categoryID"];
        } else {
            return $highestResults[0]["categoryID"];
        }
?>