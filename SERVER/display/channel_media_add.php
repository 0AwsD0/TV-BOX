<?php
    require_once("./../login_check.php");

    require("./../pdo.php");

    require("./../getid3/getid3.php");

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

    $file = explode(".",htmlentities($_POST["file"]));
    print_r($file);
    $length;

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
    // Examples
    //echo durationToSeconds("1:04:21") . "<br>"; // 3861
    //echo durationToSeconds("4:21") . "<br>";    // 261
    //echo durationToSeconds("45") . "<br>";      // 45


    if($file[1] == "jpg"){
        $length = 5; // 5 seconds default
    }
    else{
        //use PHP to determine video length and set as value
        $getID3 = new getID3;
        $videoFile = $getID3->analyze("./../media/".$_POST["file"]."");
        $lengthID3 = $videoFile['playtime_string']; //default value = 5
        echo($lengthID3);
        echo("<br>");
        $length = durationToSeconds($lengthID3);
        echo($length);
    }

    $add_query = 'INSERT INTO channels_config (file_name, duration, order_number, channel_name) VALUES (:file_name, :duration, :order_number, :channel_name)';
    $sql = $pdo->prepare($add_query);
    $sql->execute([
        ':file_name' => $_POST['file'],
        ':duration' => $length,
        ':order_number' => $order,
        ':channel_name' => $_POST['channel']
    ]);
    // Store sanitized value in session for later use
    $_SESSION["channel"] = htmlentities($_POST['channel']);
    header("Location: ./../display.php");
    exit();

?>