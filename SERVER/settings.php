<?php
require("login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX SETTINGS</title>
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
       <?PHP
                if(isset($_GET["user"])){
                    echo('
                    <div class="row item justify-content-center">
                        <div class="col-auto info-col p-0 settings-msg mb-4" style="border-radius: 15px;">
                            <p class="m-0 p-0 px-3">'.htmlentities($_GET["user"]).'</p>
                        </div>
                    </div>
                ');
                }
       ?>
    <div class="row info">
        <div class="col-lg-6 info-col">
            <p>PASSWORD CHANGE</p> <!-- PASSWORD_CHANGE_SUCCESS '?user' GET handling to do // -> pwd_... .php -->
            <p class="info-col-p"> <form action="user_pwd_change.php" method="post"> <button type="submit" style="color: white; background-color: rgba(0,0,0,0); border: 0; font-size: 60px;"><i class="fa-solid fa-key settings-ico"></i></button> </p>
            <input type="hidden" name="id" value="<?php echo($_SESSION["user_id"]); ?>"><input type="hidden" name="sender" value="settings"></form>
        </div>
        <div class="col-lg-6 info-col">
            <p>EMAIL CHANGE</p>
            <p class="info-col-p"> <form action="./user_email_change.php" method="post"> <button type="submit" style="color: white; background-color: rgba(0,0,0,0); border: 0; font-size: 60px;"><i class="fa-solid fa-at settings-ico"></i></button>  </form> </p>
        </div>
        <div class="col-lg-6 info-col">
            <p>INTERFACE PALETE</p>
            <p class="info-col-p"> <a href="#"> <i class="fa-solid fa-dice-d20 settings-ico"></i> </a> </p>
        </div>
        <div class="col-lg-6 info-col">
            <p>DOCUMENTATION</p>
            <p class="info-col-p"> <a href="./tv-box-doc/site/index.html"> <i class="fa-solid fa-book settings-ico"></i> </a> </p>
        </div>
    </div>

    <div class="row info mt-4">

    <div class="col-lg-6 info-col p-0 pt-3">
            <p>EMAIL NOTIFICATIONS</p>
            <p class="info-col-p"> <a href="#"> <i class="fa-solid fa-envelope settings-ico"></i> </a> </p>
    </div>

    <div class="col-lg-6 info-col p-0 pt-3">
        <p>ABOUT</p>
        <p class="info-col-p"> <a href="./about.php"> <i class="fa-solid fa-bolt-lightning settings-ico"></i> </a> </p>
    </div>

    </div>

 </div>
 </main>

</body>
</html>