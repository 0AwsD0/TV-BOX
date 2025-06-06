<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");

if(isset($_POST["pwd"]) && isset($_POST["mail"])){
    //create query and than header
    //+GET message for admin.php and logic there to show that user got created
    $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT); //bcrypt by default ofc

    $admin = 0;
    if($_POST["is_admin"] == "on"){
        $admin = 1;
    }

    echo("INSERT INTO users (email, password, is_admin) VALUES (`".$_POST["mail"]."`, `".$pwd."`, ".$admin.");");
    echo("<br><br>");
    $sql = 'INSERT INTO users (email, password, is_admin) VALUES (:email, :password, :is_admin)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
    ':email' => $_POST['mail'],
    ':password' => $pwd,
    ':is_admin' => $admin
    ]);
    logEvent("info", "[USER ADD] Added user: ".$_POST["mail"]." BY: ".$_SESSION["email"]);
    header("Location: admin.php?user=CREATED");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX USER ADD</title>
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
  }
label, input{
    display: block;
    font-size: 30px;
    width: 100%;
    min-width: 150px;
}
input{
    width: 90%;
    margin: auto;
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
input[type=checkbox]{
    height: 30px;
    margin-bottom: 20px;
}
</style>
    <div class="content">
        <form action="user_add.php" method="POST">
            <label for="mail">EMAIL: </label>
            <input type="mail" name="mail">
            <label for="pwd">PASSWORD: </label>
            <input type="password" name="pwd">
            <label for="is_admin">IS ADMIN?: </label>
            <input type="checkbox" name="is_admin">
            <input type="hidden" name="csrf" value="<?PHP echo($_SESSION['csrf']); ?>">
            <br>
            <button type="submit">CREATE</button>
        </form>
    </div>
</body>
</html>