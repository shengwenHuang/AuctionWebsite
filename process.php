<?php
  define("accessChecker", TRUE);

  session_start();
  $userID = $_SESSION["userID"];
  require "dbHelper.php";
  $dbHelper = new DBHelper($userID);
  
  function message_and_move($message, $movetopage) {
    header("Location: " . $movetopage . "?message=" . urlencode($message));
    exit();
  }
  
  // index.php "Login" button redirect:
  // ------------------------------------------------------------
  // Check if the submit-login button is set in the HTTP header.
  if (isset($_POST["submit-login"])) {
    // If it is, retrieve the username and password fields.
    $username = trim($_POST["username"]);
    
    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || empty($username)) {
      message_and_move("Please provide a username and password to login", "index.php");
    }
    else {
      // If the fields are not empty, set up a query to retrieve the user details for the
      // provided username, using placeholders to prevent SQL injections.
      try {
        $result = $dbHelper->fetch_user($username);
      } catch (PDOException $e) {
        message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "index.php");
      }
      
      // If the result from the query is empty, return an error message to the index page.
      if (!$result) {
        message_and_move("Could not find the provided username", "index.php");
      }
      else {
        // Else check that the password provided in the login attempt matches that of the selected user.
          if(password_verify($_POST["password"],$result["password"])) {
          // remove all session variables
          // session_unset();
          // session_destroy();
          // session_start();
          // to change a session variable, just overwrite it
          $_SESSION["username"] = "$username";
          $_SESSION["userID"] = $dbHelper ->fetch_user_id_from_username($username);
          // echo "<p>" . $_SESSION['userID'] . "hello </p>";
          message_and_move("Successfully logged in to your account!", "homepage.php");
          
        } else {
          message_and_move("Incorrect password provided, please try again", "index.php");
        }
      }
    }
  // registration.php "Register" button redirect:
  // ------------------------------------------------------------
} elseif (isset($_POST["submit-register"])) {
    // Check if the submit-register button is set in the HTTP header. If it is, retrieve the user data.
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]); // filter_var to validate with regex
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    
    // Username validation
    if(preg_match('/^\w{5,}$/', $username)) { // \w equals "[0-9A-Za-z_]"
      // valid username, alphanumeric & longer than or equals 5 chars
      $userErr = "";
    } else {
      $userErr = "<p> Please enter a usename with 5 or more characters. Only alphanumeric characters are allowed</p>";
    }
    
    // Email validation
    if (empty($email)) {
      $emailErr = "";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "<p class='errText'>Invalid email format\n</p>";
    }
    else {
      $emailErr = "";
    }
    
    // Password validation (check if > 8 characters and contains lower and upper case letter)
    if (preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $password) === 0) {
    $errPass = '<p class="errText">Password must be at least 8 characters and must contain at least one lower case letter, one upper case letter and one digit</p>';
    } else {
        $errPass = "";
      }
    
    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check that each field is not empty. If they are, return an error message to the registration page. If all fields are filled, return any validation messages to user
    if (!isset($name) || empty($name) || !isset($email) || empty($email) || !isset($username) || empty($username) || !isset($password) || empty($password) || !empty($errPass) || !empty($emailErr) || !empty($userErr)) {
      if (!empty($errPass) || !empty($emailErr) || !empty($userErr)) {
        message_and_move($userErr . $emailErr . $errPass, "registration.php");
      }
      else {
        message_and_move("Please ensure all fields have been completed", "registration.php");
      }
    } else {
      // If the fields are not empty, set up a query to check that the username hasn't been taken already.
      try {
        $result = $dbHelper->fetch_user($username);
      } catch (PDOException $e) {
        message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "register.php");
      }
      // If the result from the query is empty, the username is valid, so add the new user details to the database.
      if (!$result) {
        try {
          $result = $dbHelper->insert_user($username, $password, $email);
        } catch (PDOException $e) {
          message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "register.php");
        }
        // If the execution of the statement returned true, the insertion was successful. Otherwise, raise an error.
        if ($result) {
          message_and_move("Success! Added new user to the database" . $password, "index.php");
        } else {
          message_and_move("Error inserting user into database, user was not added", "registration.php");
        }
      }
      // If the result is not empty, return an error message to the registration page that the username is taken.
      else {
        message_and_move("That username is already taken, please try another one", "registration.php");
      }
    }
} elseif (isset($_POST["change-email"])) {
    // If it is, retrieve the username and password fields.
    $username = trim($_POST["username"]);
    $email = $_POST["newemail"];
    $password = $_POST["password"];
    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || empty($username)|| !isset($email) || empty($email)|| !isset($password) || empty($password)) {
      message_and_move("Please provide all the information", "ChangeEmail.php");
    }
    else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            message_and_move("Please provide correct email address", "ChangeEmail.php");
        }
      // If the fields are not empty, set up a query to retrieve the user details for the
      // provided username, using placeholders to prevent SQL injections.
        else {
            try {
                $result = $dbHelper->fetch_user($username);
            } catch (PDOException $e) {
                message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "ChangeEmail.php");
            }
                
            // If the result from the query is empty, return an error message to the index page.
            if (!$result) {
              message_and_move("Could not find the provided username", "ChangeEmail.php");
            }
            else {   
              // Else check that the password provided in the login attempt matches that of the selected user.
              if(password_verify($_POST["password"],$result["password"])) {
                $result = $dbHelper->update_email($email,$username);
                message_and_move("Email changed successfully! " . $_SESSION["username"], "updateAccount.php");
              } else {
                message_and_move("Incorrect password provided, please try again", "ChangeEmail.php");
              }
            }
        }
    }
} elseif (isset($_POST["change-password"])) {
    // If it is, retrieve the username and password fields.
    $username = trim($_POST["username"]);
    $previouspassword = $_POST["password"];
    $newpassword1 = $_POST["newpassword1"];
    $newpassword2 = $_POST["newpassword2"];
    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || empty($username)|| !isset($previouspassword) || empty($previouspassword)|| !isset($newpassword1) || empty($newpassword1)|| !isset($newpassword2) || empty($newpassword2)) {
      message_and_move("Please provide all the information", "ChangePassword.php");
    }
    else {
        if (!$_POST["newpassword1"] = $_POST["newpassword2"]) {
            message_and_move("Please provide similar new passwords", "ChangePassword.php");
        } else {
            if (preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $newpassword1) === 0) {
                message_and_move("Password must be at least 8 characters and must contain at least one lower case letter, one upper case letter and one digit","ChangePassword.php");
            } else {
                // If the fields are not empty, set up a query to retrieve the user details for the
                // provided username, using placeholders to prevent SQL injections.
                try {
                    $result = $dbHelper->fetch_user($username);
                } catch (PDOException $e) {
                        message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "ChangePassword.php");
                }
      
                // If the result from the query is empty, return an error message to the index page.
                if (!$result) {
                    message_and_move("Could not find the provided username", "ChangePassword.php");
                } else {
                // Else check that the password provided in the login attempt matches that of the selected user.
                    if(password_verify($_POST["password"],$result["password"])) {
                        $result = $dbHelper->change_password($newpassword1,$username);
                            message_and_move("Password changed successfully! " . $_SESSION["username"], "updateAccount.php");
                    } else {
                        message_and_move("Incorrect password provided, please try again", "ChangePassword.php");
                    }
                }
            }
        }
    }
} elseif (isset($_POST["save-auction"])) {
    // Check if the submit-register button is set in the HTTP header. If it is, retrieve the user data.
    $sellerID = $dbHelper->fetch_user_id_from_username($_SESSION["username"]);
    $itemname = $_POST["itemname"];
    $item_detail = trim($_POST["item-detail"]);
    $item_category = $_POST["category"];
    $start_price = $_POST["start-price"];
    $reserve_price = $_POST["reserve-price"];
    $day = $_POST["day"];
    $month = $_POST["month"];
    $year = $_POST["year"];
    $time = $_POST["end-time"];

    $startDatetime = date("Y-m-d H:i:s");
    $endDatetime = date("Y-m-d H:i:s", strtotime($year. "-" .$month. "-" .$day. " " .$time. ":00"));
    
    // Check that each field is not empty. If they are, return an error message to the newListings page. 
    // If all fields are filled, return any validation messages to user
    if (!isset($itemname) || empty($itemname) || !isset($item_detail) || empty($item_detail) || 
      !isset($item_category) || empty($item_category) || !isset($start_price) || empty($start_price) ||
      !isset($reserve_price) || empty($reserve_price) || !isset($day) || empty($day) || 
      !isset($month) || empty($month) || !isset($year) || empty($year)||
      !isset($time) || empty($time)) {
        message_and_move("Please ensure all fields have been completed", "newListings.php");
    } elseif ($endDatetime < $startDatetime) {
        message_and_move("The end date should not be earlier than today.", "newListings.php");
    } elseif ($start_price > $reserve_price) {
        message_and_move("The start price is higher than the reserve price.", "newListings.php");
    } else {
      try {
        $itemID = $dbHelper->insert_item($itemname, $sellerID, $item_detail);
        $categoryID = $dbHelper->fetch_categoryid_from_category($item_category);
        $insert_category_result = $dbHelper->insert_item_category($itemID, $categoryID);
        $insert_auction_result = $dbHelper->insert_auction($itemID, $start_price, $reserve_price, $startDatetime, $endDatetime);
      } catch (PDOException $e) {
            message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "newListings.php");
      }
      // If the execution of the statement returned true, the insertion was successful. Otherwise, raise an error.
      if ($insert_category_result && $insert_auction_result) {
          
        message_and_move("Success! New listing created.", "newListings.php");
      } else {
        message_and_move("Error inserting new listing.", "newListings.php");
      }
    }
} elseif (isset($_POST["new-bid-made"])) {
  // Get all of the variables that will be needed from POST, SESSION and using an SQL query
  $auctionID = $_POST["bid-auctionID"];
  $bidStartAmount = $_POST["bid-startAmount"];
  $bidAmount = $_POST["bid-amount"]*100;
  $highestBid = $dbHelper->fetch_max_bid_for_auction($auctionID)["highestBid"];
  
  // Check that the bid is not empty, that it is greater than 0 and that it is greater than the current highest bid
  if (isset($bidAmount) && !empty($bidAmount) && $bidAmount > 0 && $bidAmount > $bidStartAmount && $bidAmount > $highestBid) {
    // Get the current datetime
    $bidDatetime = date("Y-m-d H:i:s");
    // Try to add the new bid to the table
    try {
      $result = $dbHelper->insert_new_bid($userID, $auctionID, $bidAmount, $bidDatetime);
    } catch (PDOException $e) {
      $message = "Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode();
      header("Location: " . "itemAuction.php" . "?message=" . urlencode($message) . "&auctionID=" . $auctionID);
      exit();
    }
    
    // If the execution of the statement returned true, the insertion was successful. Otherwise, return to the auction page and raise an error.
    if ($result) {
      $message = "Bid was made successfully";
      header("Location: " . "itemAuction.php" . "?message=" . urlencode($message) . "&auctionID=" . $auctionID);
      exit();
    } else {
      $message = "Error adding new bid, bid was not added";
      header("Location: " . "itemAuction.php" . "?message=" . urlencode($message) . "&auctionID=" . $auctionID);
      exit();
    }
  } else {
    // The bid was invalid, so return to the item page and indicate this
    $message = "Please enter a valid bid amount";
    header("Location: " . "itemAuction.php" . "?message=" . urlencode($message) . "&auctionID=" . $auctionID);
    exit();
  }
} elseif (isset($_POST["search-button"])) {
  // Get all of the variables that will be needed from POST
  $query = $_POST["query"];
  $choices = $_POST["choices"];
  
  // If the query string length is more or less than the minimum length, then accept the query
  if (strlen($query) >= 3) {
    header("Location: " . "search.php" . "?query=" . urlencode($query) . "&choices=" . urlencode($choices));
    exit();        
  } else {
    // If query length is ltoo short, show an error message on the homepage screen
    message_and_move("Invalid query string: must be at least 3 characters", "homepage.php");
  }
} else {
  // The HTTP header does not reference a recognised button, so return an error to the index page.
  message_and_move("Direct access not permitted, redirected to login", "index.php");
}
?>