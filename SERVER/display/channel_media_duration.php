<?php
require_once("./../login_check.php");
require_once("./../pdo.php");

if(isset($_POST["record_id"])){
    $sql = 'UPDATE channels_config SET duration = :duration WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':duration' => $_POST['duration'],
        ':id' => $_POST['record_id']
    ]);
    echo("Duration changed");
}
else{
    echo("Location: ./../display.php?error=No_POST_data_detected");
}

header("Location: ./../display.php");
exit();

?>