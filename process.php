<?php include 'database.php';

  function message_and_move($message, $movetopage) {
    header("Location: " . $movetopage . "?message=" . urlencode($message));
    exit();
  }

  // index.php "Login" button redirect:
  // ------------------------------------------------------------
  // Check if the submit-login button is set in the HTTP header.
  if (isset($_POST["submit-login"])) {
    // If it is, retrieve the username and password fields.
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || $username == "" || !isset($password) || $password == "") {
      message_and_move("Please provide a username and password to login", "index.php");
    }
    else {
      // If the fields are not empty, set up a query to retrieve the user details for the
      // provided username, using placeholders to prevent SQL injections.
      try {
        $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        $result = $query->fetch();
      } catch (PDOException $e) {
        message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "index.php");
      }

      // If the result from the query is empty, return an error message to the index page.
      if (!$result) {
        message_and_move("Could not find the provided username", "index.php");
      }
      else {
        // Else check that the password provided in the login attempt matches that of the selected user.
        if ($result["password"] == $password) {
          message_and_move("Successfully logged in to your account!", "loggedin.php");
        } else {
          message_and_move("Incorrect password provided, please try again", "index.php");
        }
      }
    }
  // registration.php "Register" button redirect:
  // ------------------------------------------------------------
  } elseif (isset($_POST["submit-register"])) {
    // Check if the submit-register button is set in the HTTP header. If it is, retrieve the user data.
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check that each field is not empty. If they are, return an error message to the registration page.
    if (!isset($name) || $name == "" || !isset($email) || $email == "" || !isset($username) || $username == "" || !isset($password) || $password == "") {
      message_and_move("Please ensure all fields have been completed", "registration.php");
    } else {
      // If the fields are not empty, set up a query to check that the username hasn't been taken already.
      try {
        $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        $result = $query->fetch();
      } catch (PDOException $e) {
        message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "register.php");
      }

      // If the result from the query is empty, the username is valid, so add the new user details to the database.
      if (!$result) {
        try {
          $query = $pdo->prepare("INSERT INTO users (userType, username, password, email) VALUES (?, ?, ?, ?)");
          $result = $query->execute(array('c', $username, $password, $email));
        } catch (PDOException $e) {
          message_and_move("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "register.php");
        }

        // If the execution of the statement returned true, the insertion was successful. Otherwise, raise an error.
        if ($result) {
          message_and_move("Success! Added new user to the database", "index.php");
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