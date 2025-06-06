<?php
require("./login_check.php");
require_once("./pdo.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX DISPLAY</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="description" content="TVBOX - a management system for your TV resources.">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
    <script src="js/main.js" defer></script>
</head>
<body>
<!-- SIDE BAR -->
<main>
<?php
    require('./elements/side_bar.php');
?>
<!-- /SIDE BAR -->
 <div id="content">
       <div class="row">
            <div class="col-12">
                <div id="toggle"><div id="toggle-menu" onclick="menu_toggle()"><i class="fa-solid fa-bars"></i></div> <p id="tip"> Menu </p></div>
            </div>
       </div>
<?php

if(isset($_POST["channel"])){
    $_SESSION["channel"] = htmlentities($_POST["channel"]);
}

if(isset($_SESSION["channel"])){
    //This is here since you cant include POST variable into header() while redirecting to this website, after insert to db, prevents forced selection of same channel every insert

    //here (MAYBE) display settings to generate config -> JavaScript + 1 SQL with list of files if file is (.mp4) get it's duration in JS? Async like xml http? or letPHP handle config generation?
    //like make multiple selection box containing filenames from media directory and the extension - > nope cant change the order!
    //GENERATE form dynamically by JS than send 1 string to PHP writing file .json w name of devices that are connected to channel
    // -> you send there string w JSON and channel ID -> than it SQL 's to DB to create configs w names of all devices connected to this channel

    require("./elements/channel_select.php");
    require("./elements/channel_configure.php");
}
else
{
    require("./elements/channel_select.php");
}


?>

 </main>

</body>
</html>