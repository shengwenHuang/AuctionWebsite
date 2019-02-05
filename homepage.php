<?php 
  include "database.php";
  include "header.php";
?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>EbayLite</title>
  <!-- <link rel="stylesheet" href="css/style.css" type="text/css"> -->
</head>

<body>
  <div id="body-content">
    <header>
      <?php if (isset($_GET['message'])): ?>
        <div class="error">
          <h1><?php echo $_GET['message']; ?></h1>
        </div>
      <?php endif;?>
    </header>
  </div>
</body>

</html>