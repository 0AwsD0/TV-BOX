<?php
require("./login_check.php");
require_once("./log.php");
    if(isset($_POST["file"])){
        print_r($_POST["file"]);
        unlink('./notifications/'.$_POST["file"]);
        logEvent("info", "[NOTIFICATION DELETE] Deleted: ".'./notifications/'.$_POST["file"]." BY: ".$_SESSION["email"]);
    }
    else{
        logEvent("error", "[NOTIFICATION DELETE] Post variable not present. BY: ".$_SESSION["email"]);
        echo("Post variable not present.");
    }
    header("Location: index.php");
    exit();
?>