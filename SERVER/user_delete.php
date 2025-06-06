<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");


if($_POST["csrf"] == $_SESSION["csrf"]){
    if(isset($_POST["id"])){

        if($_POST["id"] == $_SESSION['user_id']){
            logEvent("debug", "[USER DELETE] User tried to delete himself XD - log created BY: ".$_SESSION["email"]);
            header("Location: admin.php?user=DELETION_ERROR");
            exit();
        }

        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $_POST['id']]);

        logEvent("info", "[USER DELETE] User ".$_POST["id"]." deleted BY: ".$_SESSION["email"]);
        header("Location: admin.php?user=DELETED");
        exit();
    }
    //log to add error on failed user deletion
    logEvent("error", "[USER DELETE] Error - no POST data detected.");
    header("Location: admin.php?user=DELETION_ERROR");
    exit();
}
else{
    logEvent("error", "[USER DELETE] Error - !!!CSRF!!!");
    header("Location: admin.php?user=DELETION_ERROR");
    exit();
}


?>