<?php
include("./config.php");

$displayID = $_GET['d_id'];
$sessionData ='';
date_default_timezone_set('Asia/Kolkata');
$current_time = new DateTime('now');
$current_time_formatted = $current_time->format('H:i:s');
$formFields = '
<form action="./2updateSession.php" method="POST">
<div class="mb-3">
  <input type="hidden" class="form-control" value="' . $displayID . '" id="displayID" name="displayID">
</div>
<div class="mb-3">
  <label for="name" class="form-label">Name</label>
  <input type="text" class="form-control" id="name" name="name" pattern=".{1,25}" title="Name should not contain spaces and should be maximum 20 characters long" required>
</div>

<div class="mb-3">
  <label for="MobileNumber" class="form-label">Mobile Number</label>
  <input type="text" class="form-control" id="MobileNumber" name="MobileNumber" required>
</div>
<div class="mb-3">
  <label for="playtime" class="form-label">Playtime (HH:MM)</label>
  <input type="time" class="form-control" id="playtime" name="playtime" required>
</div>
<div class="mb-3">
  <label for="Fees" class="form-label">Fees</label>
  <input type="text" class="form-control" id="Fees" name="Fees">
</div>
<div class="mb-3">
  <input type="hidden" class="form-control" value="unavailable"  name="status" >
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>';

$default_name = "";
$default_mobileNumber = 0;
$default_fees = "0";
$default_status = "available";

$sql_check = "SELECT * FROM deviceUsage WHERE displayID = '$displayID'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
  $row = mysqli_fetch_assoc($result_check);

  $sql_fetch_details = "SELECT * FROM deviceUsage WHERE displayID = '$displayID'";
  $result_fetch_details = mysqli_query($conn, $sql_fetch_details);
  if (mysqli_num_rows($result_fetch_details) > 0) {
    $row_inserted = mysqli_fetch_assoc($result_fetch_details);
    $sessionName = $row_inserted['name'];
    $sessionNumber =  $row_inserted['MobileNumber'];
    $sessionFees =  $row_inserted['Fees'];
    $sessionStatus = $row_inserted['status'];
    $sessionPlaytime = $row_inserted['playtime'];
    $sessionUpdatedtime=$row_inserted['UpdateTime'];

    list($playtime_hours, $playtime_minutes, $playtime_seconds) = explode(':', $sessionPlaytime);

    $playtime_total_seconds = ($playtime_hours * 3600) + ($playtime_minutes * 60) + $playtime_seconds;

    $updateTime_obj = DateTime::createFromFormat('H:i:s', $sessionUpdatedtime);

    $new_update_time_obj = clone $updateTime_obj;
    $new_update_time_obj->modify("+ $playtime_total_seconds seconds");

    $new_update_time_formatted = $new_update_time_obj->format('Y-m-d H:i:s');

    $time_difference = $current_time->diff($new_update_time_obj);
    $time_difference_formatted = $time_difference->format('%H:%I:%S');

    echo "<script>";
    echo "var newUpdateTime = '" . $new_update_time_formatted . "';";
    echo "</script>";


    if ($row_inserted['playtime'] === "00:00:00") {
      $sql_update = "UPDATE deviceUsage SET status = 'available' WHERE displayID = '$displayID'";
      mysqli_query($conn, $sql_update);
      $assignForm = $formFields;

    } else {
      $assignForm = "";
      $sessionData = ' <div class="container mt-5">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <span>' . $sessionName . '</span>
        </div>
        <div class="mb-3">
            <label for="MobileNumber" class="form-label">Mobile Number:</label>
            <span>' . $sessionNumber . '</span>
        </div>
        <div class="mb-3">
            <label for="playtime" class="form-label">Started at:</label>
            <span>' . $sessionUpdatedtime . '</span>
        </div>
        <div class="mb-3">
        <label for="playtime" class="form-label">Playtime:</label>
        <span id="counter"></span>
    </div>
        <form action="./2deleteSession.php" method="POST">
            <input type="hidden" name="displayID" value="'. $displayID.'">
            <button type="submit" class="btn btn-danger">End Session</button>
        </form>
    </div>';
    }
  } else {
    $sessionData = "No details found for the inserted display.";
  }
} else {
  $sql_insert = "INSERT INTO deviceUsage (displayID, name, MobileNumber, Fees, status, playtime) 
                    VALUES ('$displayID', '$default_name', '$default_mobileNumber', '$default_fees', '$default_status', '00:00:00')";
  if (mysqli_query($conn, $sql_insert)) {
    // $sessionData = "Default record inserted successfully.";
    $sql_fetch_details = "SELECT * FROM deviceUsage WHERE displayID = '$displayID'";
    $result_fetch_details = mysqli_query($conn, $sql_fetch_details);
    if (mysqli_num_rows($result_fetch_details) > 0) {
      $row_inserted = mysqli_fetch_assoc($result_fetch_details);
      $sessionName = $row_inserted['name'];
      $sessionNumber =  $row_inserted['MobileNumber'];
      $sessionFees =  $row_inserted['Fees'];
      $sessionStatus = $row_inserted['status'];
      $sessionPlaytime = $row_inserted['playtime'];


      if ($row_inserted['playtime'] === "00:00:00") {
        $sql_update = "UPDATE deviceUsage SET status = 'available' WHERE displayID = '$displayID'";
        mysqli_query($conn, $sql_update);
        $assignForm = $formFields;
      } else {
        $assignForm = "";
      }
    } else {
      $sessionData = "No details found for the inserted display.";
    }
  } else {
    $sessionData = "Error inserting default record: " . mysqli_error($conn);
  }
}

mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Forms / Elements - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    .grid-display {
      border: 1px solid #ccc;
      height: 100px;
      margin-bottom: 10px;
      text-align: center;
      padding: 10px;
    }

    .bg-danger {
      background-color: #dc3545 !important;
    }

    .bg-success {
      background-color: #28a745 !important;
    }

    .bg-black {
      background-color: #000000 !important;
      color: #ffffff;
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img height="100px" src="assets/img/gameBot-Logo.png" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Kevin Anderson</h6>
              <span>Web Designer</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="index.html">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Rooms</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="./1rooms.php">
              <i class="bi bi-circle"></i><span> Rooms</span>
            </a>
          </li>
          <li>
            <a href="./1addRoom.php">
              <i class="bi bi-circle"></i><span>Add Room</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link " data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Assign</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="./2assignRooms.php" class="active">
              <i class="bi bi-circle"></i><span>Assign Devices</span>
            </a>
          </li>

        </ul>
      </li><!-- End Forms Nav -->
      <li class="nav-item">
        <a class="nav-link " data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Bills</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="./3bill.php" class="">
              <i class="bi bi-circle"></i><span>Bills</span>
            </a>
          </li>
          <li>
            <a href="./3createBill.php">
              <i class="bi bi-circle"></i><span>Create a Bill</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->
      <li class="nav-item">
                <a class="nav-link " data-bs-target="#cafe-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-cup-straw"></i><span>Cafe</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="cafe-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="./4cafeItems.php">
                            <i class="bi bi-circle"></i><span>Cafe Items</span>
                        </a>
                    </li>
                    <li>
                        <a href="./4addCafeItems.php" class="">
                            <i class="bi bi-circle"></i><span>Add Cafe Items</span>
                        </a>
                    </li>
                </ul>
            </li><!-- End cafe Nav -->
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Assign Devices</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Assign</li>
          <li class="breadcrumb-item active">Devices</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Available rooms</h5>


              <?php echo $sessionData; ?>
              <?php echo $assignForm; ?>


            </div>
          </div>

        </div>

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  

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

        var formattedTime = pad(hours, 2) + ":" + pad(minutes, 2) + ":" + pad(seconds, 2);

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
    xhr.open("POST", "./2deleteSession.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        console.log("Delete request sent successfully to id <?php echo $displayID?>");
      }
    };
    xhr.send("displayID=<?php echo $displayID; ?>");
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