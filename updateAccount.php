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
            <h1>welcome</h1>
        </header>
<?php
//show username and email address 
echo "Username: ",$_SESSION["username"],"<br/>","Email address: ",$_SESSION["email"];
?>
    
<!--change password and email address-->
<form method="post" action="ChangeEmail.php">
    <input type="button"  value="ChangeEmail" />
    </form>
<form method="post" action="ChangePassword.php">
    <input type="button"  value="ChangePassword" />
    </form>

</body>
</html>