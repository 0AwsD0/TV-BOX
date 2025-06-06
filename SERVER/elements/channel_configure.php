<div class="row mt-3 mb-3">
        <div class="col info-col p-0" style="background-color: #404040; border-radius: 15px;">
<?php
    echo("EDITING: ".htmlentities($_SESSION["channel"]));

    $sql_devices = 'SELECT name FROM devices WHERE channel = (SELECT id FROM channels WHERE name = "'.htmlentities($_SESSION["channel"]).'");'; //works -> SELECT name FROM devices WHERE channel = (SELECT id FROM channels WHERE name = "test1");
    $result = $pdo->query($sql_devices);
    $data = $result->fetchAll(PDO::FETCH_ASSOC);

    if($data){
        echo("<br>");
        echo("DEVICES IN THIS CHANNEL: ");
        foreach($data as $row){
            echo($row["name"]." | ");
        }
    }

    if(isset($_GET["error"])){
        echo("<br>");
        echo('<span style="color: red;">'.$_GET["error"].'</span>');
    }

?>
        </div>
</div>

<?php
// can do in JS or send table to php -> get current config if exists from json for example if not using DB

/*
<input name="MyArray[]" />
<input name="MyArray[]" />
<input name="MyArray[]" />
<input name="MyArray[]" />
*/


?>
<!-- SELECTION -> select media -->
 <div>
        <?php
            define('list', TRUE);
            require("./display/channel_media_list.php");
        ?>
 </div>
 <form action="./channel_save.php" method="post" style="display: inline-block;"><button type="submit" class="display-btn"><input type="hidden" name="channel" value="<?php echo($_SESSION["channel"]); ?>">&nbsp; Save &nbsp;</button></form>
 <form action="./display/channel_clear_db.php" method="post" style="display: inline-block;"><button type="submit" class="display-btn"><input type="hidden" name="channel" value="<?php echo($_SESSION["channel"]); ?>">&nbsp; Clean Channel Configuration (RESET) &nbsp;</button></form>
<div class="row">
       <?php
        $dir = './media';
        $files = scandir($dir, 0);
        $count = 0;
        for($i = 2; $i < count($files); $i++)
        {
            /*if($count == 0 || $count%5 == 0 ){
                echo('<div class="row">');
                echo('IF COUNT1 <--'.$count.' |');
             }*/
            //print $files[$i]."<br>"; // '.' explode -> if jpg if .mp4
            $file_name = explode(".", $files[$i])[0]; // '.' explode -> if jpg if .mp4
            $file_extension = explode(".", $files[$i])[1]; // '.' explode -> if jpg if .mp4

            if($file_extension == "jpg"){
                echo('<div class="col-lg-3" style="background-color: #404040; border: 10px solid #AEAEAE; box-sizing: border-box; overflow: hidden; border-radius: 20px; position: relative;"><img class="media-img" src="./media/'.htmlspecialchars($files[$i]).'" onclick="media_tails_in(this)"><p class="media-p text-center">'.htmlspecialchars($files[$i]).'</p> <input type="hidden" name="file" value="./media/'.htmlspecialchars($files[$i]).'"> <form action="./display/channel_media_add.php" method="post">  <input type="hidden" value="'.htmlspecialchars($files[$i]).'" name="file"> <input type="hidden" value="'.htmlentities($_SESSION["channel"]).'" name="channel"> <button type="submit" class="file" onclick="addItem();"><i class="fa-solid fa-plus"></i> ADD</button> </form> </div>');
            }
            else if($file_extension == "mp4"){
                echo('<div class="col-lg-3" style="background-color: #404040; border: 10px solid #AEAEAE; box-sizing: border-box; overflow: hidden; border-radius: 20px; position: relative;"><video class="media-video" preload="none" controls src="./media/'.htmlspecialchars($files[$i]).'"></video><p class="media-p text-center">'.htmlspecialchars($files[$i]).'</p> <input type="hidden" name="file" value="./media/'.htmlspecialchars($files[$i]).'"> <form action="./display/channel_media_add.php" method="post"> <input type="hidden" value="'.htmlspecialchars($files[$i]).'" name="file"> <input type="hidden" value="'.htmlentities($_SESSION["channel"]).'" name="channel"> <button type="submit" class="file" onclick="addItem();"><i class="fa-solid fa-plus"></i> ADD</button> </form> </div>');
            }

          /*  if($count%4 == 0 && $count != 0){
                echo('</div>');
                echo('IF COUNT2 <--'.$count.' |');
            }
*/
            $count++;

        }
       ?>
       </div>