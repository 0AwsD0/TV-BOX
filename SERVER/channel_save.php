<?php
require("./login_check.php");
require("./pdo.php");
require_once("./log.php");

if(!isset($_POST["channel"])){
    header("Location: ./display.php?error=No_POST_data_detected");
    exit();
    logEvent("error", "[CFG] No POST data detected BY ".$_SESSION["email"]);
}

$cfgDate = date("Y-m-d H:i:s"); //get date now w format -> 2024-01-11 13:58:49 | Make sure the server and Device are using same timezone!
$sql = $pdo->prepare("UPDATE channels SET configuration_date = :cfgDate WHERE name = :name");
    $result = $sql->execute([
        ':cfgDate' => $cfgDate,
        ':name'    => $_SESSION["channel"]
    ]);

    logEvent("info", "[CFG] CFG FOR CHANNEL ".$_POST["channel"]." UPDATED BY ".$_SESSION["email"]);

header("Location: ./display.php?error=CFG_SAVED");
exit();

?>