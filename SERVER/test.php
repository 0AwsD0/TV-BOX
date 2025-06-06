<body style="color: white; background-color: black;">
<?php
require_once("./log.php");
logEvent("info", "test");
    /*require_once("getid3/getid3.php");

    function durationToSeconds(string $duration): int {
        $parts = explode(':', $duration);
        $count = count($parts);

        if ($count === 3) {
            // HH:MM:SS
            list($hours, $minutes, $seconds) = $parts;
            return ((int)$hours * 3600) + ((int)$minutes * 60) + (int)$seconds;
        } elseif ($count === 2) {
            // MM:SS
            list($minutes, $seconds) = $parts;
            return ((int)$minutes * 60) + (int)$seconds;
        } elseif ($count === 1) {
            // SS
            return (int)$parts[0];
        } else {
            // Invalid format
            throw new InvalidArgumentException("Invalid duration format: $duration");
        }
    }


    $getID3 = new getID3;
    $videoFile = $getID3->analyze("./media/bigvid.mp4");
    $lengthID3 = $videoFile['playtime_string']; //default value = 5
    echo($lengthID3);
    echo("<br>");
    $length = durationToSeconds($lengthID3);
    echo($length);*/
/*
require("./login_check.php");

$Time30 = strtotime('-30 minutes');// if time is between -21 and - 30min - RED
$Time20 = strtotime('-20 minutes');// if time is between -11 and - 20min - YELLOW
$Time10 = strtotime('-10 minutes');// if time is between now and - 10min - GREEN
echo(date('Y-m-d H:i:s', $Time10));
echo("<br><br>");
echo(date('Y-m-d H:i:s', $Time20));
echo("<br><br>");
echo(date('Y-m-d H:i:s', $Time30));
echo("<br><br>");
echo("<br><br>");
echo("<br><br>");
echo("<br><br>");
$Time30 = strtotime('-30 minutes');
$Time15 = strtotime('-15 minutes');

require("./pdo.php");

$sql = "SELECT * FROM `devices`;";
$result = $pdo->query($sql);
foreach($result->fetchAll(PDO::FETCH_ASSOC) as $k=>$v) {

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
        <div class="col-xl-3"><b>DEVICE NAME: </b>'.htmlspecialchars($v["name"]).'</div>
        <div class="col-xl-3"><b>LAST SEEN: </b>'.htmlspecialchars($v["last_seen"]).'</div>
        <div class="col-xl-2 text-center"><b>DB ID: </b>'.htmlspecialchars($v["id"]).'</div>
        <div class="col-xl-3 text-center">
        </div>
        <!-- Green seen in last 10 minutes (2 pings max so 1 ping had to go through) | Yellow 20mins no response | Red 30 min no response = internet lost / crash etc. | BLUE -> status unknown <- {DEFAULT} -->
        <div class="col-xl-1 text-center"><div class="dot" style="background-color: '.$color.';"></div></div>
        </div>');


}

*/
?>
<style>
    .dot{
    display: inline-block;
    background-color: blue; /*green: #00ff70 yellow: #fffb00 red: #f00*/ /* blue: blue*/
    height: 20px;
    width: 20px;
    border-radius: 100%;
    transform:translateY(10%);
    -webkit-filter: blur(2px);
            filter: blur(2px);
}

</style>
</body>