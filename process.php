<?php
  include 'database.php';

  if (isset($_POST['submit-login'])) {
    // Check that the passed variables do not contain malicious code
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    if (!isset($username) || $username == '' || !isset($password) || $password == '') {
      $error = "Please provide a username and password to login";
      header("Location: index.php?error=" . urlencode($error));
      exit();
    }
    else {
      $query = "SELECT * FROM users WHERE username='$username'";
      $result = mysqli_query($connection, $query);
      if (!$result) {
        die('Error: ' . mysqli_error($connection));
      }
      else {
        if (mysqli_num_rows($result) > 0) {
          $userdata = mysqli_fetch_assoc($result);
          if ($userdata["password"] == $password) {
            // Test code
            $error = "Success!";
            header("Location: index.php?error=" . urlencode($error));
            exit();
          } else {
            $error = "Incorrect password provided";
            header("Location: index.php?error=" . urlencode($error));
            exit();
          }
        } else {
          $error = "Could not find the provided username";
          header("Location: index.php?error=" . urlencode($error));
          exit();
        }
      }
    }
  } else {
    $error = "Something went wrong processing the data";
    header("Location: index.php?error=" . urlencode($error));
    exit();
  }
?>
