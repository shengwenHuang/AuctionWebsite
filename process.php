<?php include 'database.php';

  function error_and_return($errormessage, $returnpage) {
    header("Location: " . $returnpage . "?error=" . urlencode($errormessage));
    exit();
  }

  // Check if the submit-login button is set in the HTTP header.
  if (isset($_POST["submit-login"])) {
    // If it is, retrieve the username and password fields.
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check that these are not empty. If they are, return an error message to the index page.
    if (!isset($username) || $username == "" || !isset($password) || $password == "") {
      error_and_return("Please provide a username and password to login", "index.php");
    }
    else {
      // If the fields are not empty, set up a query to retrieve the user details for the
      // provided username, using placeholders to prevent SQL injections.
      try {
        $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        $result = $query->fetch();
      } catch (PDOException $e) {
        error_and_return("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "index.php");
      }

      // If the result from the query is empty, return an error message to the index page.
      if (!$result) {
        error_and_return("Could not find the provided username", "index.php");
      }
      else {
        // Else check that the password provided in the login attempt matches that of the selected user.
        if ($result["password"] == $password) {
          error_and_return("Success!");
        } else {
          error_and_return("Incorrect password provided, please try again", "index.php");
        }
      }
    }
  } elseif (isset($_POST["submit-register"])) {
    // Check if the submit-register button is set in the HTTP header. If it is, retrieve the user data.
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check that each field is not empty. If they are, return an error message to the registration page.
    if (!isset($name) || $name == "" || !isset($email) || $email == "" || !isset($username) || $username == "" || !isset($password) || $password == "") {
      error_and_return("Please ensure all fields have been completed", "registration.php");
    } else {
      // If the fields are not empty, set up a query to check that the username hasn't been taken already.
      try {
        $query = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $query->execute(array($username));
        $result = $query->fetch();
      } catch (PDOException $e) {
        error_and_return("Error connecting to MySQL: " . $e->getMessage() . (int)$e->getCode(), "register.php");
      }

      // If the result from the query is empty, the username is valid. Otherwise, return an error message to the registration page.
      if (!$result) {
        error_and_return("Success! Pretended to add the new user to the database", "registration.php");
      }
      else {
        error_and_return("That username is already taken, please try another one", "registration.php");
      }
    }
  } else {
    // The HTTP header does not reference a recognised button, so return an error to the index page.
    error_and_return("Something went wrong processing the data");
  }
?>