<?php
$conn=mysqli_connect("localhost", "root", "", "gameCafe");
if(mysqli_connect_errno()){
echo "Connection Fail".mysqli_connect_error();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
  ?>
