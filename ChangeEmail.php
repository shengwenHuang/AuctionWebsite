<?php
define("accessChecker", true);

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
            <h1>Change email address</h1>
        </header>

        <div id="user-input">
            <form method="post" action="process.php">
                <label for="validate">Validate your login details</label>
                <div id="validate" style="margin-bottom: 10px">
                    <input type="text" name="username" style="display: block" placeholder="Enter your username">
                    <input type="password" name="password" style="display: block" placeholder="Enter your password">
                </div>
                <label for="email">Enter your new email address</label>
                <input type="email" name="newemail" style="display: block; margin-bottom: 10px" placeholder="New email address...">
                <input id="change-email" type="submit" name="change-email" style="display: block; font-size: 1.25em"
                    value="Submit" />
            </form>
            <?php if (isset($_GET['message'])): ?>
            <div class="error">
                <?php echo $_GET['message']; ?>
            </div>
            <?php endif;?>
        </div>
    </div>
</body>

</html>