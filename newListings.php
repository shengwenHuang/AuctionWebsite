<?php 
    require "redirectIfNotLoggedIn.php";
    include "header.php";
    require "dbHelper.php";
    $dbHelper = new DBHelper($userID);
?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>New Listings</title>
</head>

<body onload="loadGen('#year', '#month', '#day')">
  <div id="body-content">
    <header>
      <h1>Add new items</h1>
    </header>
    <div id="create-item">
      <form method="post" action=process.php>
        <input type="text" id="itemname" name="itemname" placeholder="Item" /><br />
        <input type="text" id="item-detail" name="item-detail" placeholder="Tell us about this item" /><br />
        <!-- category -->
        <div>
          <div class="inner-form">
            <div class="input-field first-wrap">
              <div class="input-select">
                <select data-trigger="" id="category" name="category">
                  <option placeholder="">Category</option>
                  <?php
                  $result = $dbHelper -> get_catagories();
                  if ($result) {
                    foreach ($result as $row) {
                        echo '<option>'.$row["categoryName"].'</option>';
                    }
                }
                  ?>
                </select>
                </div>
              </div>  
            </div>
        </div>
        <!--------------->
        <input type="number" step=".01" id="start-price" name="start-price" placeholder="Start price" /><br />
        <input type="number" step=".01" id="reserve-price" name="reserve-price" placeholder="Reserve price" /><br />
        <!-- date picker -->
        <p class="fallbackLabel">Auction end date:</p>
        <div class="fallbackDatePicker">
          <span>
            <label for="day">Day:</label>
            <select id="day" name="day">
            </select>
          </span>
          <span>
            <label for="month">Month:</label>
            <select id="month" name="month">
              <option selected>01</option>
              <option>02</option>
              <option>03</option>
              <option>04</option>
              <option>05</option>
              <option>06</option>
              <option>07</option>
              <option>08</option>
              <option>09</option>
              <option>10</option>
              <option>11</option>
              <option>12</option>
            </select>
          </span>
          <span>
            <label for="year">Year:</label>
            <select id="year" name="year">
            </select>
          </span>
        </div>
        <!-- --------- -->
        <input type="time" id="end-time" name="end-time" placeholder="Auction end time" /><br />
        <input id="auction-btn" type="submit" name="save-auction" value="Create an Auction" /><br />
      </form>
      <?php if (isset($_GET['message'])): ?>
      <div class="error">
        <?php echo $_GET['message']; ?>
      </div>
      <?php endif;?>
    </div>
  </div>
  <script>
    function loadGen (year, month, day) {
      genDatePicker (year, month, day);
    }
    function genDatePicker (year, month, day){
      var fallbackPicker = document.querySelector('.fallbackDatePicker');
      var fallbackLabel = document.querySelector('.fallbackLabel');

      var yearSelect = document.querySelector(year);
      var monthSelect = document.querySelector(month);
      var daySelect = document.querySelector(day);

      fallbackPicker.style.display = 'block';
      fallbackLabel.style.display = 'block';

      // populate the days and years dynamically
      // (the months are always the same, therefore hardcoded)
      populateDays(monthSelect.value);
      populateYears();

      function populateDays(month) {
        // delete the current set of <option> elements out of the
        // day <select>, ready for the next set to be injected
        while(daySelect.firstChild){
          daySelect.removeChild(daySelect.firstChild);
        }

        // Create variable to hold new number of days to inject
        var dayNum;

        // 31 or 30 days?
        if(month === '01' || month === '03' || month === '05' || month === '07' || month === '08' || month === '10' || month === '12') {
          dayNum = 31;
        } else if(month === '04' || month === '06' || month === '09' || month === '11') {
          dayNum = 30;
        } else {
        // If month is February, calculate whether it is a leap year or not
          var year = yearSelect.value;
          var leap = (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
          dayNum = leap ? 29 : 28;
        }

        // inject the right number of new <option> elements into the day <select>
        for(i = 1; i <= dayNum; i++) {
          var option = document.createElement('option');
          j = ('0' + i).slice(-2);
          option.textContent = j;
          daySelect.appendChild(option);
        }

        // if previous day has already been set, set daySelect's value
        // to that day, to avoid the day jumping back to 1 when you
        // change the year
        if(previousDay) {
          daySelect.value = previousDay;

          // If the previous day was set to a high number, say 31, and then
          // you chose a month with less total days in it (e.g. February),
          // this part of the code ensures that the highest day available
          // is selected, rather than showing a blank daySelect
          if(daySelect.value === "") {
            daySelect.value = previousDay - 1;
          }

          if(daySelect.value === "") {
            daySelect.value = previousDay - 2;
          }

          if(daySelect.value === "") {
            daySelect.value = previousDay - 3;
          }
        }
      }

      function populateYears() {
        // get this year as a number
        var date = new Date();
        var year = date.getFullYear();

        // Make this year, and the 100 years before it available in the year <select>
        for(var i = 0; i <= 100; i++) {
          var option = document.createElement('option');
          option.textContent = year-i;
          yearSelect.appendChild(option);
        }
      }

      // when the month or year <select> values are changed, rerun populateDays()
      // in case the change affected the number of available days
      yearSelect.onchange = function() {
        populateDays(monthSelect.value);
      }

      monthSelect.onchange = function() {
        populateDays(monthSelect.value);
      }

      //preserve day selection
      var previousDay;

      // update what day has been set to previously
      // see end of populateDays() for usage
      daySelect.onchange = function() {
        previousDay = daySelect.value;
      }
    }
  </script>
</body>

</html>