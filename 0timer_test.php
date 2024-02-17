<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Counter</title>
</head>

<body>

  <div id="counter"></div>

  <?php
  include("./config.php");

  date_default_timezone_set('Asia/Kolkata');

  $current_time = new DateTime('now');

  $current_time_formatted = $current_time->format('H:i:s');

  $id = 10;

  $sql_fetch_data = "SELECT playtime, UpdateTime FROM deviceUsage WHERE ID = $id";
  $result_fetch_data = mysqli_query($conn, $sql_fetch_data);

  if (mysqli_num_rows($result_fetch_data) > 0) {
    while ($row = mysqli_fetch_assoc($result_fetch_data)) {
      $playtime = $row['playtime'];
      $updateTime = $row['UpdateTime'];

      list($playtime_hours, $playtime_minutes, $playtime_seconds) = explode(':', $playtime);

      $playtime_total_seconds = ($playtime_hours * 3600) + ($playtime_minutes * 60) + $playtime_seconds;

      $updateTime_obj = DateTime::createFromFormat('H:i:s', $updateTime);

      $new_update_time_obj = clone $updateTime_obj;
      $new_update_time_obj->modify("+ $playtime_total_seconds seconds");

      $new_update_time_formatted = $new_update_time_obj->format('Y-m-d H:i:s');

      $time_difference = $current_time->diff($new_update_time_obj);
      $time_difference_formatted = $time_difference->format('%H:%I:%S');

      echo "<script>";
      echo "var newUpdateTime = '" . $new_update_time_formatted . "';";
      echo "</script>";
    }
  } else {
    echo "No data found for ID = $id in deviceUsage table.";
  }

  mysqli_close($conn);
  ?>

  <script>
    var timerInterval;

    function updateCounter() {
      var currentTime = new Date();

      if (newUpdateTime) {
        var updateTime = new Date(newUpdateTime);

        var difference = updateTime - currentTime;

        if (difference > 0) {
          var seconds = Math.floor(difference / 1000);

          var hours = Math.floor(seconds / 3600);
          seconds -= hours * 3600;
          var minutes = Math.floor(seconds / 60);
          seconds -= minutes * 60;

          var formattedTime = hours + ":" + pad(minutes, 2) + ":" + pad(seconds, 2);

          document.getElementById("counter").innerHTML = "Time Remaining: " + formattedTime;
        } else {
          document.getElementById("counter").innerHTML = "Time Passed: 00:00:00";
          clearInterval(timerInterval);
          sendDeleteRequest(); // Call function to send delete request
        }
      }
    }

    function sendDeleteRequest() {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "http://localhost/gamebot-admin/2deleteSession.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          console.log("Delete request sent successfully to id <?php echo $id?>");
        }
      };
      xhr.send("device_id=<?php echo $id; ?>");
    }

    function pad(num, size) {
      var s = num + "";
      while (s.length < size) s = "0" + s;
      return s;
    }

    updateCounter();

    timerInterval = setInterval(updateCounter, 1000);
  </script>

</body>

</html>
