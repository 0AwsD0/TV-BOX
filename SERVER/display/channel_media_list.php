<?php
//prevents direct access /requiring once login_check wont work here and after include in the same time
//script including this file needs -> define('list', TRUE);
if(!defined('list')) {
    exit('Direct access not permitted');
 }

if(isset($_SESSION["channel"])){
    require_once("login_check.php");
    $media_query = 'SELECT * FROM channels_config WHERE channel_name = :channel_name ORDER BY order_number ASC';
    $sql = $pdo->prepare($media_query);
    $sql->execute([':channel_name' => $_SESSION['channel']]);
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    echo('<div class="row mt-3 mb-3 channel">');
        echo('<div class="col-xl-4"><b>NAME</b></div>');
        echo('<div class="col-xl-2 text-center"><b>DURATION</b></div>');
        echo('<div class="col-xl-1 text-center"><b>ORDER</b></div>');
        echo('<div class="col-xl-5 text-center"><b>OPTIONS</b></div>');
    echo('</div>');

    $i = 1;
    foreach($result as $k=>$v){
        echo('<div class="row mt-3 mb-3 channel">');
            echo('<div class="col-xl-4">'.htmlspecialchars_decode($v["file_name"]).'</div>');
            echo('<div class="col-xl-2 text-center"><form action="./display/channel_media_duration.php" method="post" style="display: inline-block;"> <input type="hidden" name="record_id" value="'.htmlentities($v["id"]).'"> <input type="number" name="duration" value="'.htmlentities($v["duration"]).'" style="display: inline-block; width: 30%; background-color: black; border: 0; color: white; border-radius: 5px; padding-left: 5px;" ><button type="submit" style="border: 0; color: white; background-color: black; margin-left: 10px; padding-left: 10px; padding-right: 10px; border-radius: 5px;"><b><i class="fa-solid fa-hourglass-half"></i> Apply</b></button></form></div>');
            echo('<div class="col-xl-1 text-center">'.$i.'</div>'); //for DB order -> echo('<div class="col-xl-2">ORDER '.htmlentities($v["order_number"]).'</div>');
            echo('<div class="col-xl-5 text-center">
            <form action="./display/channel_media_move.php" method="post" style="display: inline-block;"> <input type="hidden" name="record_id" value="'.htmlentities($v["id"]).'"><input type="hidden" name="move" value="up"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><b><i class="fa-solid fa-arrow-up"></i> MOVE UP</b></button></form>
            <form action="./display/channel_media_move.php" method="post" style="display: inline-block;"> <input type="hidden" name="record_id" value="'.htmlentities($v["id"]).'"><input type="hidden" name="move" value="down"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><b>MOVE DOWN <i class="fa-solid fa-arrow-down"></i></b></button></form> |
            <form action="./display/channel_media_delete.php" method="post" style="display: inline-block;"> <input type="hidden" name="record_id" value="'.htmlentities($v["id"]).'"><button type="submit" style="border: 0; color: white; background-color: rgba(0,0,0,0);"><b><i class="fa-solid fa-trash"></i> DELETE</b></button></form>
            </div>');
        echo('</div>');
        $i++;
    }

}
else{
    echo("ERROR: Session channel not set.");
}

?>