<?php
  require "dbHelper.php";
  $dbHelper = new DBHelper(null);
?>
<!doctype html>
<html>
<head>
</head>
<body>
    <div id="body-content">
        <header>
            <h1>Change email address</h1>
        </header>

        <div id="user-input">
            <form method="post" action="process.php">
                <input type="text" name="username" placeholder="Enter your username"> <br/>
                <input type="password" name="password" placeholder="Enter your password"> <br/>
                <input type="email" name="newemail" placeholder="Enter your new Email address"> <br/>
                <input id="change-email" type="submit" name="change-email" value="submit" />
            </form>
            <?php if (isset($_GET['message'])) : ?>
            <div class="error">
                <?php echo $_GET['message']; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>