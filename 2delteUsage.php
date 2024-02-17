<?php
$displayID = $_GET['displayId'];
include("./config.php");
$sql_delete = "DELETE FROM deviceUsage WHERE displayID = '$displayID'";
if (mysqli_query($conn, $sql_delete)) {
    // Successful deletion, redirect to another page
    header("Location: ./2assignSession.php?d_id=$displayID");
    exit(); // Make sure to exit the script after redirection
} else {
    echo "Error deleting data from deviceUsage: " . mysqli_error($conn);
}
?>
