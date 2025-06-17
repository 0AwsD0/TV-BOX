<?php
require("./login_check.php");
require("./log.php");
require("./pdo.php");
    $pdo->beginTransaction();
try{
        if(isset($_POST["id"])){
            $sql1 = $pdo->prepare("DELETE FROM channels_config WHERE channel_name = (SELECT name FROM channels WHERE channels.id = :id);");
            $delete = $sql1->execute([':id' => $_POST['id']]);

            $sql2 = $pdo->prepare("DELETE FROM channels WHERE channels.id = :id");
            $delete = $sql2->execute([':id' => $_POST['id']]);
            $pdo->commit();
            logEvent("info", "[CHANNEL DELETE] ".$_POST["id"]." created BY: ".$_SESSION["email"]);
        }
}catch(Exception $err){
    $pdo->rollBack();
    logEvent("error", "[CHANNEL DELETE] ERROR - TRANSACTION ROLLBACK - Deletion ERROR: ".$err." | ID: ".$_POST["id"]." created BY: ".$_SESSION["email"]);
}

header("Location: channels.php");
exit();
?>
