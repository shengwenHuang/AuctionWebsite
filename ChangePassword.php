<?php
    define("accessChecker", TRUE);
    
    require "redirectIfNotLoggedIn.php";
    require "dbHelper.php";
    $dbHelper = new DBHelper(null);
    require "header.php";
?>

<!doctype html>
<html>

<body>
    <div id="body-content">
        <header>
            <h1>Change Password</h1>
        </header>

        <div id="user-input">
            <form method="post" action="process.php">
                <label for="validate">Validate your login details</label>
                <div id="validate" style="margin-bottom: 10px">
                    <input type="text" name="username" style="display: block" placeholder="Enter your username">
                    <input type="password" name="password" style="display: block" placeholder="Current password">
                </div>
                <label for="password-inputs">Enter your new password</label>
                <div id="password-inputs" style="margin-bottom: 10px">
                    <input type="password" name="newpassword1" style="display: block" placeholder="New password...">
                    <input type="password" name="newpassword2" style="display: block" placeholder="Re-enter new password...">
                </div>
                <input id="change-password" type="submit" name="change-password" value="Submit" />
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