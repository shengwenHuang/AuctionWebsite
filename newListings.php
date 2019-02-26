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

<body onload="loadGen('#year1', '#month1', '#day1', '#year2', '#month2', '#day2')">
  <div id="body-content">
    <header>
      <h1>Add new items</h1>
    </header>
    <div id="create-item">
      <form method="post" action=process.php>
        <input type="text" id="itemname" name="itemname" placeholder="Item" /><br />
        <input type="text" id="item-detail" name="item-detail" placeholder="Tell us about this item" /><br />
        <input type="text" id="item-category" name="item-category" placeholder="category" /><br />
        <input type="number" id="start-price" name="start-price" placeholder="Start price" /><br />
        <input type="number" id="reserve-price" name="reserve-price" placeholder="Reserve price" /><br />
        <form>
          <p class="fallbackLabel">Auction start date:</p>
          <div class="fallbackDatePicker">
            <span>
              <label for="day1">Day:</label>
              <select id="day1" name="day1">
              </select>
            </span>
            <span>
              <label for="month1">Month:</label>
              <select id="month1" name="month1">
                <option selected>January</option>
                <option>February</option>
                <option>March</option>
                <option>April</option>
                <option>May</option>
                <option>June</option>
                <option>July</option>
                <option>August</option>
                <option>September</option>
                <option>October</option>
                <option>November</option>
                <option>December</option>
              </select>
            </span>
            <span>
              <label for="year1">Year:</label>
              <select id="year1" name="year1">
              </select>
            </span>
          </div>
        </form>
        <form>
          <p class="fallbackLabel">Auction end date:</p>
          <div class="fallbackDatePicker">
            <span>
              <label for="day2">Day:</label>
              <select id="day2" name="day1">
              </select>
            </span>
            <span>
              <label for="month2">Month:</label>
              <select id="month2" name="month2">
                <option selected>January</option>
                <option>February</option>
                <option>March</option>
                <option>April</option>
                <option>May</option>
                <option>June</option>
                <option>July</option>
                <option>August</option>
                <option>September</option>
                <option>October</option>
                <option>November</option>
                <option>December</option>
              </select>
            </span>
            <span>
              <label for="year2">Year:</label>
              <select id="year2" name="year2">
              </select>
            </span>
          </div>
        </form>
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
    function loadGen (year1, month1, day1, year2, month2, day2) {
      genDatePicker (year1, month1, day1);
      genDatePicker (year2, month2, day2);
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
        if(month === 'January' || month === 'March' || month === 'May' || month === 'July' || month === 'August' || month === 'October' || month === 'December') {
          dayNum = 31;
        } else if(month === 'April' || month === 'June' || month === 'September' || month === 'November') {
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
          option.textContent = i;
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