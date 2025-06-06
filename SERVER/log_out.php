<?php
include("./log.php");
session_start();
$_SESSION['logged_in'] = false;
session_destroy();

logEvent("info", "[LOG OUT] User logged out: ".$_SESSION["email"]);

header("Location: login.php");
exit();

?>
Logged Out