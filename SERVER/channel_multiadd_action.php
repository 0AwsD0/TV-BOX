<?php
require("./login_check.php");
require_once("./log.php");
require_once("./pdo.php");
require_once("./getid3/getid3.php");

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedChannels = $_POST['channel'] ?? [];
    $file = $_POST['file'] ?? null;

    if ($file && !empty($selectedChannels)) {

        $file_extension = explode(".", $file)[1];
        echo($file_extension);

        if($file_extension == "jpg"){
            $length = 5; // 5 seconds default
        }
        else{
            //use PHP to determine video length and set as value
            $getID3 = new getID3;
            $videoFile = $getID3->analyze("./media/".$file."");
            $lengthID3 = $videoFile['playtime_string']; //default value = 5
            echo($lengthID3);
            echo("<br>");
            $length = durationToSeconds($lengthID3);
            echo($length);
        }

        $pdo->beginTransaction();
        try{
            foreach ($selectedChannels as $channel) {
                $order = 0;
                $select_query = 'SELECT * FROM channels_config WHERE channel_name = :channel_name ORDER BY order_number DESC LIMIT 1';
                $sql = $pdo->prepare($select_query);
                $sql->execute([':channel_name' => $_POST['channel']]);
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                if(!$row){
                    $order = 1;
                }
                else{
                    print_r($row);
                    $lastValue = $row["order_number"];
                    $order = $lastValue + 1;
                    echo("<br>");
                }

                $add_query = 'INSERT INTO channels_config (file_name, duration, order_number, channel_name) VALUES (:file_name, :duration, :order_number, :channel_name)';
                $sql = $pdo->prepare($add_query);
                $sql->execute([
                    ':file_name' => $file,
                    ':duration' => $length,
                    ':order_number' => $order,
                    ':channel_name' => $channel
                ]);
            }
        }catch(Exception $err){
            $pdo->rollBack();
            header("Location: ./channel_multiadd.php?status=ERROR");
            logEvent("error", "[MULTIADD] Multiadd FAILED! Error: ".$err." BY: ".$SESSION['email']);
            exit();
        }
            $pdo->commit();
            logEvent("info", "[MULTIADD] Multiadd SUCCESS! BY: ".$SESSION['email']);



        $query = http_build_query([
            //'added' => $file,  <<if you want to include the file that was just added
            'channel' => $selectedChannels  // this will become channel[]=Testo&channel[]=Viron
        ]);

        header("Location: ./channel_multiadd.php?$query");
        exit();
    } else {
        // Optional: Handle missing data
        header("Location: ./channel_multiadd.php?status=ERROR");
        logEvent("error", "[MULTIADD] Multiadd FAILED! Missing POST data. BY: ".$SESSION['email']);
        echo "No file or channels selected.";
        exit();
    }
}
?>