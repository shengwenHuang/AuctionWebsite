<?php
    define("accessChecker", TRUE);
    require "dbHelper.php";
    $dbHelper = new DBHelper(null);
?>

<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>EbayLite Registration</title>
</head>

<body>
    <div id="body-content">
        <header>
            <h1>New User Registration</h1>
        </header>

        <div id="user-input">
            <form method="post" action="process.php">
                <input type="text" name="name" placeholder="Enter your name"> <br/>
                <input type="text" name="email" placeholder="Enter your email"> <br/>
                <input type="text" name="username" placeholder="Enter your username"> <br/>
                <input type="password" name="password" placeholder="Enter your password"> <br/>
                <input id="finish-registration" type="submit" name="submit-register" value="Register" />
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