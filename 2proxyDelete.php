<?php
include("./config.php");

$display_id = $_GET['display_id'];

$sql = "SELECT ip_address FROM Displays WHERE display_id = $display_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $ip_address = $row['ip_address'];

    mysqli_close($conn);

    $name = $_GET['name'];
    $minutes = $_GET['minutes'];

    $target_url = "http://$ip_address/submit?name=$name&minutes=0";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch); 

    curl_close($ch);

    if ($response === false) {
        echo "cURL Error: " . $error;
    } else {
        echo $response;
    }
} else {
    echo "Display ID not found.";
}
?>
