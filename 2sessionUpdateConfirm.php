<?php
include("./config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $displayID = $_POST['displayID'];
    $name = $_POST['name'];
    $MobileNumber = $_POST['MobileNumber'];
    $playtime = $_POST['playtime'];
    $Fees = $_POST['Fees'];
    $status = $_POST['status'];
    

    $sql_update = "UPDATE deviceUsage SET 
                    name = '$name', 
                    MobileNumber = '$MobileNumber', 
                    playtime = '$playtime', 
                    Fees = '$Fees', 
                    status = '$status' 
                    WHERE displayID = '$displayID'";

    if (mysqli_query($conn, $sql_update)) {
        echo "successful";
    } else {
        echo "Error updating data: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>