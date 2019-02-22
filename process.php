<?php 
  require "dbHelper.php";
  $dbHelper = new DBHelper(null);
  session_start();

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
          message_and_move("Successfully logged in to your account! " . $_SESSION["username"], "homepage.php");
        } else {
          message_and_move("Incorrect password provided, please try again", "homepage.php");
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

} elseif (isset($_POST["change-email"])) {
    // If it is, retrieve the username and password fields.
    $username = trim($_POST["username"]);
    $newemail = trim($_POST["newemail"]);
    $password = $_POST["password"];
    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || empty($username)) {
      message_and_move("Please provide a username", "ChangeEmail.php");
    }
    else {
      // If the fields are not empty, set up a query to retrieve the user details for the
      // provided username, using placeholders to prevent SQL injections.
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
          // remove all session variables
          // session_unset();
          // session_destroy();
          // session_start();
          // to change a session variable, just overwrite it
          $_SESSION["username"] = "$username";
          $_SESSION["userID"] = $dbHelper ->fetch_user_id_from_username($username);
          $email = $newemail;
          // echo "<p>" . $_SESSION['userID'] . "hello </p>";
          message_and_move("Email Address has been changed successfully " . $_SESSION["username"], "updateAccount.php");
          

        } else {
          message_and_move("Incorrect password provided, please try again", "ChangeEmail.php");
        }
      }
    }

 } elseif (isset($_POST["change-password"])) {
    // If it is, retrieve the username and password fields.
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $newpassword1 = $_POST["newpassword1"];
    $newpassword2 = $_POST["newpassword2"];
    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || empty($username)) {
      message_and_move("Please provide a username", "ChangePassword.php");
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
                if($newpassword1!==$newpassword2){
              message_and_move("Please enter the same new passwords", "ChangePassword.php");
                }else{
          // Else check that the password provided in the login attempt matches that of the selected user.
                   if(password_verify($_POST["password"],$result["password"])) {
          // remove all session variables
          // session_unset();
          // session_destroy();
          // session_start();
          // to change a session variable, just overwrite it
          $_SESSION["username"] = "$username";
          $_SESSION["userID"] = $dbHelper ->fetch_user_id_from_username($username);
          $password = $newpassword1;
          // echo "<p>" . $_SESSION['userID'] . "hello </p>";
          message_and_move("Password has been changed successfully " . $_SESSION["username"], "updateAccount.php");
          

                } else {
          message_and_move("Incorrect password provided, please try again", "ChangePassword.php");
                }
            }
        }
    }
    // Check that each field is not empty. If they are, return an error message to the registration page. If all fields are filled, return any validation messages to user
    if (!isset($name) || empty($name) || !isset($email) || !isset($newemail)|| empty($email) || !isset($username) || empty($username) || !isset($password) || empty($password) || !empty($errPass) || !empty($emailErr) || !empty($userErr)) {
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
  } else {
    // The HTTP header does not reference a recognised button, so return an error to the index page.
    message_and_move("Something went wrong processing the data");
  }
?>