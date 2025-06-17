<?php
    require("./login_check.php");
    require_once("./log.php");
    require("./pdo.php");

if(isset($_POST["id"]) && isset($_POST["name"])){
    $pdo->beginTransaction();
    try{
        $sql = $pdo->prepare("UPDATE channels_config SET channel_name = :name WHERE channel_name = (SELECT name FROM channels WHERE id = :id );");
        $sql->execute([
            ':name' => $_POST['name'],
            ':id'   => $_POST['id']
        ]);

        $sql2 = $pdo->prepare("UPDATE channels SET name = :name WHERE id = :id");
        $sql2->execute([
            ':name' => $_POST['name'],
            ':id'   => $_POST['id']
        ]);

        $pdo->commit();
        logEvent("info", "[CHANNEL RENAME] ".$_POST["id"]." renamed to ".$_POST["name"]." BY: ".$_SESSION["email"]);
        header("Location: channels.php");
        exit();
    }catch(Exception $err){
        $pdo->rollBack();
        logEvent("error", "[CHANNEL RENAME] ERROR - SQL TRANSACTION ROLLBACK - Error: ".$err." | ID: ".$_POST["id"]." NAME: ".$_POST["name"]." BY: ".$_SESSION["email"]);
        header("Location: channels.php");
        exit();
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX - CHANNEL RENAME</title>
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body style="background-color: #313131;">
<div class="content">
<form action="" method="post">
<input type="hidden" name="id" value="<?php echo(htmlspecialchars($_POST["id"])); ?>">
<div style="font-family: 'Ubuntu', sans-serif; display: inline-block; margin: 0; padding: 0; font-weight: bold; font-size: 25px;">ENTER NEW CHANNEL NAME:</div>
<input type="text" name="name" id="name"style="border-radius: 15px; border: 0;padding: 0; margin: 0; padding-left: 10px; padding-right: 10px; font-size: 20px;">&nbsp;&nbsp;<button type="submit" style="color: white; border: 0; border-radius: 15px; background-color: rgb(0, 0, 0); font-size: 25px;">&nbsp;CHANGE&nbsp;</button>
</form>
</div>
<style>
.content {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: white;
}
</style>
</body>
</html