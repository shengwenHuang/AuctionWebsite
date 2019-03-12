<?php
  define("accessChecker", TRUE); 
  
  require "redirectIfNotLoggedIn.php";  
  require "dbHelper.php";
  $dbHelper = new DBHelper(null);
  require "header.php";
?>

<!doctype html>
<html>

<head>
</head>

<body>
  <div id="body-content">
    <header>
      <?php if (isset($_GET['message'])): ?>
      <h1>
        <?php echo $_GET['message']; ?>
      </h1>
      <?php endif;?>
    </header>
  </div>

  <?php
    //show username and email address
    $username = $_SESSION["username"];
    $email = $dbHelper->fetch_user_email_from_username($username);
    echo "Username: ". $_SESSION["username"] . "<br/> Email address: " . $email;
  ?>

  <!--change password and email address-->
  <form method="post" action="changeEmail.php">
    <input type="submit" value="ChangeEmail" />
  </form>
  <form method="post" action="changePassword.php">
    <input type="submit" value="ChangePassword" />
  </form>

</body>

</html>