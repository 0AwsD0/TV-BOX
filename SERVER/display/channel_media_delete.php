<?php
require_once("./../login_check.php");
require_once("./../pdo.php");

print_r($_POST);

if(isset($_POST["record_id"])){
    $sql = 'DELETE FROM channels_config WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_POST['record_id']]);
    echo("Deleted");
}
else{
    echo("Location: ./../display.php?error=No_POST_data_detected");
}

header("Location: ./../display.php");
exit();

?>