<?php
require("login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX</title>
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
    <div class="row info">
        <div class="col-lg-6 col-xl-4 info-col">
            <p>Connected Devices</p>
            <p class="info-col-p">
                <?php
                    require_once("./pdo.php");
                    $sql = "SELECT count(devices.name) FROM devices WHERE 1;";
                    $result = $pdo->query($sql);
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    echo($row['count(devices.name)']);
                ?>
            </p>
        </div>
        <div class="col-lg-6 col-xl-4 info-col">
            <p>Active Channels</p>
            <p class="info-col-p">
            <?php
                require_once("./pdo.php");
                $sql2 = "SELECT count(channels.name) FROM channels WHERE 1;";
                $result2 = $pdo->query($sql2);
                $row2 = $result2->fetch(PDO::FETCH_ASSOC);
                echo($row2['count(channels.name)']);
            ?>
            </p>
        </div>
        <div class="col-lg-6 col-xl-4 info-col">
            <p>Notifications</p>
            <p class="info-col-p">
                <?php
                    $sql3 = "SELECT count(notifications.title) FROM notifications;";
                    $result3 = $pdo->query($sql3);
                    $row3 = $result3->fetch(PDO::FETCH_ASSOC);
                    echo($row3['count(notifications.title)']);

                /* OLD FS Based Solution
                    $count = 0;
                    $path    = './notifications';
                    $files = scandir($path);
                    foreach($files as $file){
                        $file_name = explode(".",$file);
                        if($file_name[1] == "txt"){
                            $count++;
                        }
                    }
                    echo($count);
                    */
                ?>
                 </p>
        </div>
    </div>

    <div class="row notification mt-5">

<?php
    $sql4 = "SELECT * FROM notifications;";
    $result4 = $pdo->query($sql4);
    foreach($result4->fetchAll(PDO::FETCH_ASSOC) as $row4){
        if($row4['type'] == 'ERROR'){
            $color = ["bg-danger", "text-danger"];
            $ico = '<i class="fa-solid fa-circle-exclamation"></i>';
        }elseif($row4['type'] == 'WARNING')
        {
            $color = ["bg-warning", "text-warning"];
            $ico = '<i class="fa-solid fa-triangle-exclamation"></i>';
        }
        else{
            $color = ["bg-primary", "text-primary"];
            $ico = '<i class="fa-solid fa-circle-question"></i>';
        }
        echo('<div class="col-xl-6">
                <div class="card text-white '.$color[0].' mb-3 home-card">
                <div class="card-header fs-2 text-white fw-bold">'.$ico.'&nbsp;');
        echo($row4['title']);
        echo('  </div>
                <div class="card-body bg-white" style="border-radius: 0px 0px 30px 30px">
                <h5 class="card-title fs-3 '.$color[1].' bg-white fw-bold">'.($row4['content']).'</h5>
        '); //header
            echo('
            </div>
                <h4 style="margin: auto;"> <form action="notification_delete.php" method="post"> <input type="hidden" name="id" value="'.$row4['id'].'"> <button class="nt-del" type="submit" style="color: white; border: 0px; padding: 10px; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i> DELETE</button></form> </h4>
            </div>
            </div>
            ');
    }

    /* OLD Solution for file as notification
    foreach($files as $file){
        $file_name = explode(".",$file);
        if($file_name[1] == "txt"){
                $lines = file("./notifications/".$file);
                $color = '';
                if(substr($file_name[0], 0, 1) == 'E'){
                    $color = ["bg-danger", "text-danger"];
                    $ico = '<i class="fa-solid fa-circle-exclamation"></i>';
                }elseif(substr($file_name[0],0, 1) == 'W')
                {
                    $color = ["bg-warning", "text-warning"];
                    $ico = '<i class="fa-solid fa-triangle-exclamation"></i>';
                }
                else{
                    $color = ["bg-primary", "text-primary"];
                    $ico = '<i class="fa-solid fa-circle-question"></i>';
                }
                echo('<div class="col-xl-6">
                        <div class="card text-white '.$color[0].' mb-3 home-card">
                        <div class="card-header fs-2 text-white fw-bold">'.$ico.'&nbsp;');
                echo($file_name[0]);
                echo('  </div>
                        <div class="card-body bg-white" style="border-radius: 0px 0px 30px 30px">
                        <h5 class="card-title fs-3 '.$color[1].' bg-white fw-bold">'.htmlspecialchars($lines[0]).'</h5>
                '); //header

            foreach($lines as $line){
                echo('  <p class="card-text fs-4 '.$color[1].' fw-bold m-0">');
                echo(htmlspecialchars($line));
                echo('  </p>');
            }
                echo('
                </div>
                    <h4 style="margin: auto;"> <form action="notification_delete.php" method="post"> <input type="hidden" name="file" value="'.$file.'"> <button class="nt-del" type="submit" style="color: white; border: 0px; padding: 10px; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i> DELETE</button></form> </h4>
                </div>
                </div>
                ');
        }
    }*/
?>
    </div>
<?php
    //BASE SCRIPT I WROTE
    // $path    = './notifications';
    // $files = scandir($path);
    // foreach($files as $file){
    //     if(explode(".",$file)[1] == "txt"){
    //         echo($file); //header
    //         $lines = file("./notifications/".$file);
    //         echo("  Lines count:  ".count($lines)."  ");
    //         foreach($lines as $line){
    //             echo($line);
    //         }
    //     }
    // }
?>

<!-- TEMPLATE

<div class="col-xl-6">
        <div class="card text-white bg-danger mb-3 home-card">
            <div class="card-header fs-2 text-white fw-bold"><i class="fa-solid fa-circle-question"></i> Error - form /errors file name -> device name</div>
            <div class="card-body bg-white" style="border-radius: 0px 0px 30px 30px">
                <h5 class="card-title fs-3 text-danger bg-white fw-bold">ERROR</h5>
                <p class="card-text fs-4 text-danger fw-bold">form /errors file insides -> message (DB conneciton lost need rebooting)  Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <h4 style="margin: auto;"> <form action="notification_delete.php" method="post"> <input type="hidden" name="file" value="<?php echo("d"); ?>"> <button class="nt-del" type="submit" style="color: white; border: 0px; padding: 10px; background-color: rgba(0,0,0,0);"><i class="fa-solid fa-trash"></i> DELETE</button></form> </h4>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card text-white bg-danger mb-3 home-card">
            <div class="card-header fs-2 text-white fw-bold"><i class="fa-solid fa-circle-exclamation"></i> Error - form /errors file name -> device name</div>
            <div class="card-body bg-white" style="border-radius: 0px 0px 30px 30px">
                <h5 class="card-title fs-3 text-danger bg-white fw-bold">ERROR</h5>
                <p class="card-text fs-4 text-danger fw-bold">form /errors file insides -> message (DB conneciton lost need rebooting)  Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
    </div>


    <div class="col-xl-6">
            <div class="card text-dark bg-warning mb-3 home-card">
                <div class="card-header fs-2 text-white fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Warning - Pliki zajmują już 10 GB na serwerze</div>
                <div class="card-body bg-white" style="border-radius: 0px 0px 30px 30px">
                    <h5 class="card-title fs-3 text-warning fw-bold">WARNING</h5>
                    <p class="card-text fs-4 text-warning fw-bold">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
            </div>
        </div>
-->
    </div>

 </div>
 </main>

</body>
</html>