<?php
require("./login_check.php");
require("./log.php");
require("./pdo.php");

if(isset($_POST["id"])){
    $sql = $pdo->prepare("DELETE FROM channels WHERE channels.id = :id");
    $delete = $sql->execute([':id' => $_POST['id']]);
    logEvent("info", "[CHANNEL DELETE] ".$_POST["id"]." created BY: ".$_SESSION["email"]);
}

header("Location: channels.php");
exit();
?>
