<?php
    require("./login_check.php");
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] = true && $_SESSION['privileges'] == 1){
        //you can place for any logic required on load
    }
    else{
        echo("You don't have privileges to see this page.");
        exit();
    }

    require('./pdo.php');
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX</title>
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
    <div class="row info">
        <div class="col-lg-6 col-xl-4 info-col">
            <p>User count:</p>
            <p class="info-col-p">
                <?php
                    $sql = "SELECT count('email') FROM `users` WHERE 1;";
                    $result = $pdo->query($sql);
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    echo($row["count('email')"]);
                ?>
            </p>
        </div>
        <div class="col-lg-6 col-xl-4 info-col">
            <p>Admin user count:</p>
            <p class="info-col-p">
                <?php
                    $sql = "SELECT count('email') FROM `users` WHERE users.is_admin = 1;";
                    $result = $pdo->query($sql);
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    echo($row["count('email')"]);
                ?>
            </p>
        </div>
        <div class="col-lg-6 col-xl-4 info-col">
            <p>Create User</p>
            <p class="info-col-p"><a href="user_add.php" style="cursor: pointer;"><i class="fa-solid fa-user-plus user-ico"></i></a></p>
            <?php
    if(isset($_GET['user'])){
        echo('<div class="row justify-content-md-center">
            <div class="col-md-auto text-center panel_msg"><b>USER '.htmlspecialchars($_GET["user"]).'</b></div></div>'); //make it some div down below? maybe?
    }
?>
        </div>
    </div>

    <div class="row channel">
        <div class="col-xl-2 text-center"><b>DB ID</b></div>
        <div class="col-xl-3 text-center"><b>EMAIL</b></div>
        <div class="col-xl-2 text-center"><b>ADMIN</b></div>
        <div class="col-xl-5 text-center"><b>OPTIONS</b></div>
    </div>

    <?php

    $sql = "SELECT * FROM `users`;";
    $result = $pdo->query($sql);
    foreach($result->fetchAll(PDO::FETCH_ASSOC) as $k=>$v) {
        echo('
        <div class="row channel">
            <div class="col-xl-2 text-center">'.htmlspecialchars($v["id"]).'</div>
            <div class="col-xl-3 text-center">'.htmlspecialchars($v["email"]).'</div>
            <div class="col-xl-2 text-center">');
            if($v["is_admin"] == 1)
            {
                echo("YES");
            }
            else{
                echo("NO");
            }
            echo('</div><div class="col-xl-5 text-center">
                <form action="user_pwd_change.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><input type="hidden" name="csrf" value="'.$_SESSION["csrf"].'"><input type="hidden" name="sender" value="admin"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);" name="send"><i class="fa-solid fa-repeat"></i><b> PASSWORD CHANGE</b></button></form>
                <form action="user_delete.php" method="post" style="display: inline-block;"><input type="hidden" name="id" value="'.htmlspecialchars($v["id"]).'"><input type="hidden" name="csrf" value="'.$_SESSION["csrf"].'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i><b> DELETE</b></button></form>
            </div>
        </div>
        ');
    }
    ?>
    <br>

    <div class="row info">
        <div class="col-12">
        <p class="logs-header"><b>LOGS</b></p> <!-- May be good ideo to make logs split every week? or certain weight of file? or daily? or not? -->
        </div>
        <div class="col-12">
        <div class="logs">
        <?php
        $logFile = "./log/".date('Y_m_d').".log"; // Ensure this file is in the same directory as the script
        if (file_exists($logFile)) {
            $lines = file($logFile);
            foreach ($lines as $line) {
                // Extract log level
                preg_match('/\[(INFO|ERROR|DEBUG|WARNING)\]/', $line, $matches);
                $level = $matches[1] ?? 'INFO';
                echo "<div class='log-line $level'>" . htmlspecialchars($line) . "</div>";
            }
        } else {
            echo "<p style='color: red;'>Log file not found.</p>";
        }
        ?>
        </div>
        </div>
    </div>

 </div>
 </main>
<style>
.logs-header{
    color: white;
    margin: 20px;
    font-size: 30px;
}
.logs{
    background-color: white;
    margin: 10px;
    margin-top: 0;
    margin-bottom: 30px;
    padding: 5px;
    max-height: 500px;
    overflow-y: scroll;
    font-family:'Courier New', Courier, monospace;
    font-size: 20px;
    font-weight: bold;
background-color: black;
}
.log-container {
    /*background: #fff;*/
    background-color: black;
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
    /*box-shadow: 0 0 10px rgba(0,0,0,0.1);*/
}
.log-line {
    border-bottom: 1px solid rgb(77, 77, 77); /*#eee*/
    padding: 4px 0;
}
.INFO { color: green; }
.ERROR { color: red; }
.DEBUG { color: gray; }
.WARNING { color: orange; }
</style>
</body>
</html>