<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");

if(isset($_POST["name"])){
    if ($name !== '' && strlen($name) < 70) {
        $sql = $pdo->prepare("INSERT INTO channels (name) VALUES (:name)");
        $result = $sql->execute([':name' => $_POST['name']]);
        logEvent("info", "[CHANNEL CREATE] ".$_POST["name"]." created BY: ".$_SESSION["email"]);
    }
}
header("Location: channels.php");
exit();
?>