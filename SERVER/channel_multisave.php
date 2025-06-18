<?php
require("./login_check.php");
require_once("./log.php");
require_once("./pdo.php");

print_r($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedChannels = $_POST['channel'] ?? [];
    $file = $_POST['file'] ?? null;


try{
    $pdo->beginTransaction();
    $cfgDate = date("Y-m-d H:i:s"); //get date now w format -> 2024-01-11 13:58:49 | Make sure the server and Device are using same timezone!

    foreach($selectedChannels as $channel){
        $sql = $pdo->prepare("UPDATE channels SET configuration_date = :cfgDate WHERE name = :name");
            $result = $sql->execute([
                ':cfgDate' => $cfgDate,
                ':name'    => $channel
    ]);

    logEvent("info", "[MULTISAVE] CFG'S SAVED | BY ".$_SESSION["email"]);
    }

}catch(Exception $err){
            $pdo->rollBack();
            header("Location: ./channel_multiadd.php?status=ERROR");
            logEvent("error", "[MULTISAVE] Multisave FAILED! Error: ".$err." BY: ".$_SESSION['email']);
            exit();
        }
            $pdo->commit();
            logEvent("info", "[MULTISAVE] Multisave SUCCESS! BY: ".$_SESSION['email']);



        $query = http_build_query([
            //'added' => $file,  <<if you want to include the file that was just added
            'channel' => $selectedChannels  // this will become channel[]=Testo&channel[]=Viron
        ]);

        header("Location: ./channel_multiadd.php?$query");
        exit();
    } else {
        // Optional: Handle missing data
        header("Location: ./channel_multiadd.php?status=ERROR");
        logEvent("error", "[MULTISAVE] Multisave FAILED! Missing POST data. BY: ".$_SESSION['email']);
        exit();
    }

?>