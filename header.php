<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    .dropbtn {
      background-color: #4CAF50;
      color: white;
      padding: 16px;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .dropbtn:hover, .dropbtn:focus {
      background-color: #3e8e41;
    }

    #myInput {
      background-position: 14px 12px;
      background-repeat: no-repeat;
      font-size: 16px;
      padding: 14px 20px 12px 45px;
      border: none;
      border-bottom: 1px solid #ddd;
    }

    #myInput:focus {outline: 3px solid #ddd;}

    .dropdown {
      position: relative;
      float: right;
      padding-right: 10em;
      padding-top: 0.5em;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f6f6f6;
      min-width: 230px;
      overflow: auto;
      border: 1px solid #ddd;
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
    }

    .dropdown a:hover {background-color: #ddd;}

    .show {display: block;}
  </style>
</head>

<body>
  <div class="dropdown" , alig>
    <button onclick="myFunction()" class="dropbtn">My EbayLite</button>
    <div id="myDropdown" class="dropdown-content">
      <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
      <a href="homepage.php" style="font-weight: bold">Home page</a>
      <a href="myBids.php">My bids</a>
      <a href="watchlist.php">My watchlist</a>
      <a href="purchaseHistory.php">Purchase history</a>
      <a href="yourListings.php">My listings</a>
      <a href="salesHistory.php">Sales history</a>
      <a href="updateAccount.php">Update account info</a>
      <a href="index.php" style="font-weight: bold">Logout</a>
    </div>
  </div>

  <script>
    // When the user clicks on the button, toggle between hiding and showing the dropdown content
    function myFunction() {
      document.getElementById("myDropdown").classList.toggle("show");
    }

    function filterFunction() {
      var input, filter, ul, li, a, i;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      div = document.getElementById("myDropdown");
      a = div.getElementsByTagName("a");
      for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          a[i].style.display = "";
        } else {
          a[i].style.display = "none";
        }
      }
    }
  </script>
</body>

</html>
