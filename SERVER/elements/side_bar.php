<nav class="side-bar">
    <div id="title-holder" style="width: 100%; margin-bottom: 20px;"><p class="bar-title">TV BOX PANEL</p></div>
    <div class="option "><a href="./index.php"><div class="menu-button"><i class="fa-solid fa-house" style="position: absolute; left: 10px; top: 10px"></i> Home</div></a></div>
    <div class="option"><a href="./devices.php"><div class="menu-button"><i class="fa-solid fa-cube" style="position: absolute; left: 10px; top: 10px"></i> Devices</div></a></div>
    <div class="option"><a href="./channels.php"><div class="menu-button"><i class="fa-solid fa-forward-step" style="position: absolute; left: 10px; top: 10px"></i> Channels</div></a></div>
    <div class="option"><a href="./display.php"><div class="menu-button"><i class="fa-solid fa-desktop" style="position: absolute; left: 10px; top: 10px"></i> Display</div></a></div>
    <div class="option"><a href="./media.php"><div class="menu-button"><i class="fa-solid fa-folder-open" style="position: absolute; left: 10px; top: 10px"></i> Media</div></a></div>
    <div id="menu-spacer" style="height: 15vh;"></div>
    <?php
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] = true && $_SESSION['privileges'] == 1){
            echo(' <div class="option"><a href="./admin.php"><div class="menu-button"><i class="fa-solid fa-unlock" style="position: absolute; left: 10px; top: 10px"></i> Admin Panel</div></a></div>');
        }
    ?>
            <div id="logged-in">
                <p style="font-size: 15px; margin-bottom: 0;">Legged in as:</p>
                <p style="font-size: 25px; margin: 0;">
                <?php
                    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] = true){
                        echo($_SESSION['email']);
                    }
                    else{
                        echo("USERNAME");
                    }
                ?>
                </p>
            </div>
    <div class="option"><a href="./log_out.php"><div class="menu-button"><i class="fa-solid fa-right-from-bracket" style="transform: rotate(180deg); position: absolute; left: 10px; top: 10px"></i> Logout</div></a></div>
    <div class="option"><a href="./settings.php"><div class="menu-button"><i class="fa-solid fa-gear" style="position: absolute; left: 10px; top: 10px"></i>  Settings</div></a></div>
    <div class="option"><a href="./tv-box-doc/site/index.html"><div class="menu-button" style="margin-left: 9%; margin-right: 11%; width: 80%; border-radius: 10px; margin-top: 30px;"><i class="fa-solid fa-circle-question"></i> Documentation</div></a></div>
</nav>