<?php
include("./config.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $displayID = $_POST['displayID'];
    $sql_fetch_data = "SELECT * FROM deviceUsage WHERE displayID = '$displayID'";
    $result_fetch_data = mysqli_query($conn, $sql_fetch_data);

    if (mysqli_num_rows($result_fetch_data) > 0) {
        while ($row = mysqli_fetch_assoc($result_fetch_data)) {
            $name = $row['name'];
            $MobileNumber = $row['MobileNumber'];
            $playtime = $row['playtime'];
            $InTime = $row['InTime'];
            $OutTime = $row['OutTime'];
            $Fees = $row['Fees'];

            $sql_insert = "INSERT INTO historyUsage (displayID, name, MobileNumber, playtime, InTime, OutTime, Fees, UpdationDate) 
                            VALUES ('$displayID', '$name', '$MobileNumber', '$playtime', '$InTime', '$OutTime', '$Fees', CURRENT_TIMESTAMP())";
            mysqli_query($conn, $sql_insert);
        }
        // echo "Data inserted into historyUsage successfully.";


    } else {
        echo "No data found for the specified displayID.";
    }
}
mysqli_close($conn);
?>


<script>
    var playtimeSplit = "<?php echo $playtime ?>".split(":");
    var hours = parseInt(playtimeSplit[0]);
    var minutes = parseInt(playtimeSplit[1]);

    var totalMinutes = (hours * 60) + minutes;

    var url = "./2proxyDelete.php?name=<?php echo str_replace(' ', '', $name) ?>&minutes=" + totalMinutes + "&display_id=<?php echo $displayID; ?>";

    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var responseData = xhr.responseText;
            console.log(responseData);
            console.log("URL access successful");
            window.location.href="2delteUsage.php?displayId=<?php echo $displayID; ?>";
        }
    };
    xhr.send();
</script>


<?php include("./0loader.php");?>