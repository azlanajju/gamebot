<?php
include("./config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $displayID = $_POST['displayID'];
    $name = $_POST['name'];
    $MobileNumber = $_POST['MobileNumber'];
    $playtime = $_POST['playtime'];
    $Fees = $_POST['Fees'];
    $status = $_POST['status'];
    
    mysqli_close($conn);
}
?>
<script>
    var playtimeSplit = "<?php echo $playtime ?>".split(":");
    var hours = parseInt(playtimeSplit[0]);
    var minutes = parseInt(playtimeSplit[1]);

    var totalMinutes = (hours * 60) + minutes;

    var url = "./2proxy.php?name=<?php echo str_replace(' ', '', $name) ?>&minutes=" + totalMinutes + "&display_id=<?php echo $displayID; ?>";

    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var responseData = xhr.responseText;
            console.log(responseData);

            var formData = new FormData();
            formData.append('displayID', '<?php echo $displayID ?>');
            formData.append('name', '<?php echo $name ?>');
            formData.append('MobileNumber', '<?php echo $MobileNumber ?>');
            formData.append('playtime', '<?php echo $playtime ?>');
            formData.append('Fees', '<?php echo $Fees ?>');
            formData.append('status', '<?php echo $status ?>');

            var postRequest = new XMLHttpRequest();
            postRequest.onreadystatechange = function() {
                if (postRequest.readyState === 4 && postRequest.status === 200) {
                    console.log("POST request successful");
                    window.location.href = "./2assignSession.php?d_id=<?php echo $displayID ?>";
                    console.log(postRequest.responseText);
                }
            };
            postRequest.open("POST", "./2sessionUpdateConfirm.php", true);
            postRequest.send(formData);
        }
    };
    xhr.send();
</script>


<?php include("./0loader.php");?>