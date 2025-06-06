<?php

require_once("./../login_check.php");
require("./../pdo.php");

if(!isset($_POST["channel"])){
    header("Location: ./../display.php?error=No_POST_data_detected");
    exit();
}

$sql = 'DELETE FROM channels_config WHERE channel_name = :channel_name';
$stmt = $pdo->prepare($sql);
$stmt->execute([':channel_name' => $_POST['channel']]);

header("Location: ./../display.php");
exit();

?>