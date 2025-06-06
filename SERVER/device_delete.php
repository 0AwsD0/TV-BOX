<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");

if(isset($_POST["id"])){
    echo(htmlspecialchars($_POST["id"]));
    $sql = $pdo->prepare("DELETE FROM devices WHERE id = :id");
    $delete = $sql->execute([':id' => $_POST["id"]]);
}
logEvent("info", "Device ".$_POST["id"]." deleted BY: ".$_SESSION["email"]);
header("Location: devices.php");
exit();
?>
