<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try{
    $pdo->beginTransaction();

    $copy_name = $_POST["name"]."_copy";
    $id = $_POST["id"];

    $sql = "INSERT INTO channels(name) VALUES (:name)";
    $query = $pdo->prepare($sql);
    $query->execute([":name" => $copy_name]);

    $sql2 = "SELECT * FROM channels_config WHERE channel_name = :name;";
    $query2 = $pdo->prepare($sql2);
    $query2->execute([":name" => $_POST["name"]]);
    $result = $query2->fetchAll();

        $sql3 = "INSERT INTO channels_config(file_name,duration,order_number,channel_name) VALUES ";
        foreach($result as $row){
            print_r("<br><br>");
            $sql3 .= "('".$row["file_name"]."',".$row["duration"].",".$row["order_number"].",'".$copy_name."'),";
        }
        $sql3 = substr($sql3, 0, -1);
        $sql3 .= ";";
        //print_r($sql3);
        $pdo->query($sql3);

        $pdo->commit();
        logEvent("info", "[CHANNEL COPY] Channel ".htmlentities($_POST["name"])." copy created. BY ".$_SESSION["email"]);
}
catch(Exception $err){
        $pdo->rollBack();
        logEvent("error", "[CHANNEL COPY] ERROR - SQL TRANSACTION ROLLBACK - Could not insert data into database. Error: ".$err." BY ".$_SESSION["email"]);
        //header("Location: channels.php");
        exit();
}

//header("Location: channels.php");
exit();
?>