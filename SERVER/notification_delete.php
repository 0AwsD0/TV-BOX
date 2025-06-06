<?php
require("./login_check.php");
require("./pdo.php");
require("./log.php");
    if(isset($_POST["id"])){
        $delete = "DELETE FROM notifications WHERE id = :id";
        $sql = $pdo->prepare($delete);
        $sql->execute([':id' => $_POST['id']]);
        logEvent("info", "[NOTIFICATION DELETE] Deleted Notification id:".$_POST["id"]." BY: ".$_SESSION["email"]);
    }
    else{
        echo("No id provided.");
    }
    header("Location: index.php");
    exit();
?>