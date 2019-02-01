<html>
    <head>
        <!-- <link rel="stylesheet" type="text/css" href="/nxjwolf/styles.css" /> -->
    </head>
    <body>

    <?php
// define variables and initialize with empty values
$nameErr = $emailErr = $usernameErr = $passwordErr = "";
$name = $email = $username = $password = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $nameErr = "Missing";
    }
    else {
        $name = $_POST["name"];
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email address missing";
    }
    else {
        $name = $_POST["email"];
    }

    if (empty($_POST["username"])) {
        $usernameErr = "Username address missing";
    }
    else {
        $name = $_POST["username"];
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password address missing";
    }
    else {
        $name = $_POST["password"];
    }

}
?>

<form method="POST"
 action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

    Name: <input type="text" name="name" value="<?php echo $name;?>">
    
    <?php echo $nameErr;?>
    <br />

    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <?php echo $emailErr;?>
    <br />

    Username: <input type="text" name="username" value="<?php echo $username;?>">
    <?php echo $usernameErr;?>
    <br />
    
    Password: <input type="text" name="password" value="<?php echo $password;?>">
    <?php echo $passwordErr;?>
    <br />

    <button type="submit" value="Submit">Submit</button>

    <?php
if (isset($_GET["submit"])) {
    // process the form contents...
}
?>
  

        <?php
            // $mysqli = new mysqli("localhost", "mysqlread", "Welcome1998", "nxj");
            // if ($mysqli->connect_errno) {
            //     printf("Connect failed: %s\n", $mysqli->connect_error);
            //     exit();
            // }
           

            // $result = $mysqli->query("SELECT tb_merchant.name, tb_merchant.website, tb_merchant_location.location, tb_merchant_location.phone_number, tb_merchant_location.rating from tb_merchant, tb_merchant_location where tb_merchant.pk_merchant_id = fk_merchant_id");

            
            // $result->close();
            // $avVal->close();
            // $mysqli->close();
        
        ?>
            
    </body>
</html>
