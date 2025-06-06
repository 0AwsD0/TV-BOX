<?php
require("./login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX DEVICES</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="description" content="TVBOX - a management system for your TV resources.">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
    <script src="js/main.js" defer></script>
</head>
<body>
<!-- SIDE BAR -->
<main>
<?php
    require('./elements/side_bar.php');
    require('./pdo.php');
?>
<!-- /SIDE BAR -->
 <div id="content">
       <div class="row">
            <div class="col-12">
                <div id="toggle"><div id="toggle-menu" onclick="menu_toggle()"><i class="fa-solid fa-bars"></i></div> <p id="tip"> Menu </p></div>
            </div>
       </div>
    <div class="row channel">
        <div class="col-xl-3 text-center"><b>DEVICE NAME</b></div>
        <div class="col-xl-3 text-center"><b>LAST SEEN</b></div>
        <div class="col-xl-2 text-center"><b>DB ID</b></div>
        <div class="col-xl-3 text-center"><b>OPTIONS</b></div>
        <div class="col-xl-1 text-center"><b>STATUS</b></div>
    </div>

    <?php
    $Time30 = strtotime('-30 minutes');
    $Time15 = strtotime('-15 minutes');

    $sql = "SELECT * FROM `devices`;";
    $result = $pdo->query($sql);
    foreach($result->fetchAll(PDO::FETCH_ASSOC) as $k=>$v) {


        //status color
        $device_time = $v["last_seen"]; //'2025-04-23 10:20:00'; for tests

        $color = "blue";

        if (!empty($device_time) && $device_time !== '0') {
            $now = new DateTime();
            $last_seen = new DateTime($device_time);
            $diff_in_minutes = ($now->getTimestamp() - $last_seen->getTimestamp()) / 60;

            if ($diff_in_minutes <= 15) {
                $color = "#00ff70";  // Active in last 15 min
            } elseif ($diff_in_minutes <= 30) {
                $color = "#fffb00"; // Active in last 30 min
            } else {
                $color = "#f00";    // Inactive (over 30 min)
            }
        }

        echo('
        <div class="row channel">
            <div class="col-xl-3 text-center">'.htmlspecialchars($v["name"]).'</div>
            <div class="col-xl-3 text-center">'.htmlspecialchars($v["last_seen"]).'</div>
            <div class="col-xl-2 text-center">'.htmlspecialchars($v["id"]).'</div>
            <div class="col-xl-3 text-center">
            <form action="device_channel.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-pen-to-square"></i><b> CHANNEL</b></button></form>
            <form action="device_delete.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i><b> DELETE</b></button></form>
            </div>
            <!-- Green seen in last 10 minutes (2 pings max so 1 ping had to go through) | Yellow 20mins no response | Red 30 min no response = internet lost / crash etc. | BLUE -> status unknown <- {DEFAULT} -->
            <div class="col-xl-1 text-center"><div class="dot" style="background-color: '.$color.';"></div></div>
            </div>');


    }
    ?>
<!--
    <div class="row channel">
        <div class="col-lg-3"><b>DEVICE NAME: </b>WAW1</div>
        <div class="col-lg-3"><b>LAST SEEN: </b>2025-02-22 12:34:56</div>
        <div class="col-lg-2 text-center"><b>DB ID: </b>1</div>
        <div class="col-lg-2 text-center"><b>OPTIONS: </b>DELETE</div>  //Green seen in last 10 minutes (2 pings max so 1 ping had to go through) | Yellow 15mins no response | Red 30 min no response = internet lost / crash etc. | BLUE -> status unknown <- DEFAULT change color in JS or PHP
        <div class="col-lg-2 text-center"><div class="dot"></div></div>
    </div>

    <div class="row channel">
        <div class="col-lg-3"><b>DEVICE NAME: </b>Krakov-Site-1</div>
        <div class="col-lg-3"><b>LAST SEEN: </b>2024-11-23 09:56:01</div>
        <div class="col-lg-2 text-center"><b>DB ID: </b>22</div>
        <div class="col-lg-2 text-center"><b>OPTIONS: </b>DELETE</div>
        <div class="col-lg-2 text-center"><div class="dot"></div></div>
    </div> -->


</div>

 </div>
 </main>

</body>
</html>