<?php
require("./login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX CHANNELS</title>
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
<!-- Edit button displays page that allows adding removing devices from channel pool -->
    <div class="row channel">
        <div class="col-xl-3 text-center"><b>CHANNEL NAME</b></div>
        <div class="col-xl-2 text-center"><b>CONFIG DATE</b></div>
        <div class="col-xl-2 text-center"><b>DB ID</b></div>
        <div class="col-xl-5 text-center"><b>OPTIONS</b></div>
    </div>
    <?php

    require("./pdo.php");

    $sql = "SELECT * FROM `channels`;";
    $result = $pdo->query($sql);
    foreach($result->fetchAll(PDO::FETCH_ASSOC) as $k=>$v) {
        echo('
        <div class="row channel">
            <div class="col-xl-3 text-center"><b></b>'.htmlspecialchars($v["name"]).'</div>
            <div class="col-xl-2 text-center"><b></b>'.htmlspecialchars($v["configuration_date"]).'</div>
            <div class="col-xl-2 text-center"><b></b>'.htmlspecialchars($v["id"]).'</div>
            <div class="col-xl-5 text-center">
            <form action="display.php" method="post" style="display: inline-block;"><input type="hidden" name="channel" value="'.htmlspecialchars($v["name"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-desktop"></i><b> DISPLAY</b></button></form>
            <form action="channel_rename.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-pen-to-square"></i><b> RENAME</b></button></form>
            <form action="channel_copy.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><input type="hidden" name="name" value="'.htmlspecialchars($v["name"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-copy"></i><b> COPY</b></button></form>
            <form action="channel_delete.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i><b> DELETE</b></button></form>
            </div>
        </div>
        ');
    }
    ?>
    <!--
    <div class="row channel">
        <div class="col-md-3"><b>NAME: </b>This is name of the channel</div>
        <div class="col-md-3"><b>CFG: </b>24-04-2023</div>
        <div class="col-md-2 text-center"><b>DB ID: </b>Nr in db</div>
        <div class="col-md-4 text-center"><b>OPTIONS: </b>This are settings - buttons edit/delete</div>
    </div>
    <div class="row channel">
        <div class="col-md-4"><b>NAME: </b>This is name of the channel</div>
        <div class="col-md-2"><b>CFG: </b>24-04-2023</div>
        <div class="col-md-2 text-center"><b>DB ID: </b>Nr in db</div>
        <div class="col-md-3 text-center"><b>OPTIONS: </b>This are settings - buttons edit/delete</div>
    </div>
    <div class="row channel">
        <div class="col-md-4"><b>NAME: </b>This is name of the channel</div>
        <div class="col-md-2"><b>CFG: </b>24-04-2023</div>
        <div class="col-md-2 text-center"><b>DB ID: </b>Nr in db</div>
        <div class="col-md-4 text-center"><b>OPTIONS: </b>This are settings - buttons edit/delete</div>
    </div>
    <div class="row channel">
        <div class="col-md-4"><b>NAME: </b>This is name of the channel</div>
        <div class="col-md-2"><b>CFG: </b>24-04-2023</div>
        <div class="col-md-2 text-center"><b>DB ID: </b>Nr in db</div>
        <div class="col-md-4 text-center"><b>OPTIONS: </b>This are settings - buttons edit/delete</div>
    </div>
-->
    <div class="row add-channel">
        <div class="col-12">
            <form action="channel_create.php" method="post"><b>NAME:</b>&nbsp;&nbsp;<input type="text" name="name" style="border-radius: 15px; border: 0; padding-left: 10px; padding-right: 10px;">&nbsp;&nbsp;<button type="submit" style="color: white; border: 0; border-radius: 15px; background-color: rgb(0, 0, 0);"><b>&nbsp;CREATE </b><i class="fa-solid fa-plus"></i>&nbsp;</button></form>
        </div>
    </div>

 </div>
 </main>

</body>
</html>