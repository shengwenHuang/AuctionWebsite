<?php
  require "redirectIfNotLoggedIn.php";
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
            <h1>Change Password</h1>
        </header>

        <div id="user-input">
            <form method="post" action="process.php">
                <input type="text" name="username" placeholder="Enter your username"> <br/>
                <input type="password" name="password" placeholder="Enter your current password"> <br/>
                <input type="password" name="newpassword1" placeholder="Enter your new Password"> <br/>
                <input type="password" name="newpassword2" placeholder="Re-enter your new Password"> <br/>
                <input id="change-password" type="submit" name="change-password" value="submit" />
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