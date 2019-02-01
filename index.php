<?php include "database.php" ?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>EbayLite</title>
    <!-- <link rel="stylesheet" href="css/style.css" type="text/css"> -->
  </head>
  <body>
    <div id="container">
      <header>
        <h1>Welcome to EbayLite!</h1>
      </header>
      <div id="input">
        <form method="post" action=process.php>
          <input type="text" id="username" name="username" placeholder="Enter Your Username"/><br/>
          <input type="password" id="password" name="password" placeholder="Enter Your Password"/><br/>
          <input id="login-btn" type="submit" name="submit-login" value="Login"/><br/>
        </form>
        <?php if (isset($_GET['error'])) : ?>
          <div class="error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>
      </div>
      <div id="register-button">
        <form method="post" action="registration.php">
          <input id="register-btn" type="submit" name="" value="Register"/>
        </form>
      </div>
    </div>
  </body>
</html> 
