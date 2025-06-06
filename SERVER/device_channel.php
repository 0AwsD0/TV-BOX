<?php
require("./login_check.php");

require("./pdo.php");

if(isset($_POST["id"]) && isset($_POST["channel"])){
    $sql = $pdo->prepare("UPDATE devices SET channel = :channel WHERE id = :id");
    $update = $sql->execute([
        ':channel' => $_POST['channel'],
        ':id'      => $_POST['id']
    ]);
    echo("Upd");
    header("Location: devices.php");
    exit();
}

if(isset($_POST["id"]) && !isset($_POST["channel"])){
    $sql = "SELECT channels.name, channels.id FROM channels;";
    $select = $pdo->query($sql);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX CHANNEL SELECT</title>
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body style="background-color: #313131; font-family: 'Ubuntu', sans-serif;">
<style>
.content {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: white;
    background-color: black;
    padding: 10px;
    border-radius: 15px;
    padding: 40px;
  }
label{
    display: block;
    font-size: 30px;
    width: 100%;
    min-width: 150px;
}
select{
    width: 100%;
    margin: auto;
    font-size: 30px;
    margin-bottom: 20px;
}
label{
    margin-top: 20px;
    margin-bottom: 10px;
    text-align: center;
}
button{
    margin: auto;
    display: block;
    font-size: 25px;
    color: white;
    background-color: black;
    border: 3px solid white;
    border-radius: 15px;
    padding: 10px;
    cursor: pointer;
    margin-bottom: 20px;
}
button:hover{
    background-color: #313131;
}
button:active{
    background-color:rgb(29, 29, 29);
}
#login-failed{
    width: 100%;
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    color: white;
    background-color: red;
    padding-top: 20px;
    padding-bottom: 20px;
}
</style>
    <div class="content">
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo(htmlentities($_POST["id"])); ?>">
            <label for="channel">Select Channel:</label>
                <select name="channel" id="channel">
                    <?php
                        foreach($select->fetchAll(PDO::FETCH_ASSOC) as $k=>$v){
                            echo('<option value="'.htmlentities($v["id"]).'">'.htmlentities($v["name"]).'</option>');
                        }
                    ?>
                </select>
            <br>
            <button type="submit">SAVE</button>
        </form>
    </div>
</body>
</html>