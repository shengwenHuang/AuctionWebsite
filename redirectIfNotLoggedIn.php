<?php
if(!defined("accessChecker")) {
    die("Direct access not permitted");
}

session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] == '') {
    header("Location: ./index.php");
}
else {
    $userID = $_SESSION["userID"];
}
?>
