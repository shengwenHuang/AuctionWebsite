<?php
  require "dbHelper.php";
  $dbHelper = new DBHelper(null);
?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>New Listings</title>
</head>

<body>
  <div id="body-content">
    <header>
      <h1>Add new items</h1>
    </header>
    <div id="create-item">
      <form method="post" action=process.php>
        <input type="text" id="itemname" name="itemname" placeholder="Item" /><br />
        <input type="text" id="item-detail" name="item-detail" placeholder="Tell us about this item" /><br />
        <input type="text" id="item-category" name="item-category" placeholder="category" /><br />
      </form>
      <?php if (isset($_GET['message'])): ?>
      <div class="error">
        <?php echo $_GET['message']; ?>
      </div>
      <?php endif;?>
    </div>
    <div id="create-auction">
      <form method="post" action=process.php>
        <input type="number" id="start-price" name="start-price" placeholder="Start price" /><br />
        <input type="number" id="reserve-price" name="reserve-price" placeholder="Reserve price" /><br />
        <input type="text" id="start-datetime" name="start-datetime" placeholder="Auction start date" /><br />
        <input type="text" id="end-datetime" name="end-datetime" placeholder="Auction end date" /><br />
        <input id="auction-btn" type="submit" name="save-auction" value="Create an Auction" /><br />
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