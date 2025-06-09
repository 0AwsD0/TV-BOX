<?php
require("./login_check.php");
require_once("./log.php");
require("./pdo.php");


if(isset($_POST["id"]) && isset($_POST["pwd"]) && isset($_POST["sender"]) && isset($_POST["csrf"])){

    if($_POST["pwd"] == '' && $_POST["sender"] == "settings"){
        logEvent("warning", "[PWD CHANGE] Pwd empty - settings.php BY: ".$_SESSION["email"]);
        header("Location: ./settings.php?user=PASSWORD_CHANGE_FAILED");
        exit();
    }
    else if($_POST["pwd"] == '' && $_POST["sender"] == "admin"){
        logEvent("warning", "[PWD CHANGE] Pwd empty - admin.php BY: ".$_SESSION["email"]);
        header("Location: ./admin.php?user=PASSWORD_CHANGE_FAILED");
        exit();
    }

    if(strlen($_POST["pwd"]) < 10 && $_POST["sender"] == "settings"){
        logEvent("warning", "[PWD CHANGE] Pwd too short - settings.php BY: ".$_SESSION["email"]);
        header("Location: ./settings.php?user=PASSWORD_CHANGE_FAILED (TOO SHORT - MINIMUM 10 CHARACTERS)");
        exit();
    }
    elseif(strlen($_POST["pwd"]) < 10 && $_POST["sender"] == "admin"){
        logEvent("warning", "[PWD CHANGE] Pwd too short - admin.php BY: ".$_SESSION["email"]);
        header("Location: ./admin.php?user=PASSWORD_CHANGE_FAILED (TOO SHORT - MINIMUM 10 CHARACTERS)");
        exit();
    }

    if($_POST["csrf"] == $_SESSION["csrf"]){

        if($_POST["sender"] == "admin"){
            $password = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
            $sql = 'UPDATE users SET password = :password WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':password' => $password,
                ':id' => $_POST['id']
            ]);
            logEvent("info", "[PWD CHANGE] User id: ".$_POST["id"]." - admin.php BY: ".$_SESSION["email"]);
            header("Location: ./admin.php?user=PASSWORD_CHANGE_SUCCESS");
            exit();
        }
        $password = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
        $sql = 'UPDATE users SET password = :password WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':password' => $password,
            ':id' => $_SESSION['user_id']
        ]);
        logEvent("info", "[PWD CHANGE] settings.php BY: ".$_SESSION["email"]);
        header("Location: ./settings.php?user=PASSWORD_CHANGE_SUCCESS");
        exit();
    }
    else{
        if($_POST["sender"] == "admin"){
            logEvent("error", "[PWD CHANGE] !!!CSRF!!! admin.php BY: ".$_SESSION["email"]);
            header("Location: ./admin.php?user=CSRF_FAIL");
            exit();
        }
            logEvent("error", "[PWD CHANGE] !!!CSRF!!! settings.php BY: ".$_SESSION["email"]);
            header("Location: ./settings.php?user=CSRF_FAIL"); //logic in settings needed to make window appear etc.
            exit();
    }
}
//if(isset($_POST["id"]) && isset($_POST["sender"]) && isset($_POST["csrf"])) // <- if this is true just render site and make form than send w PWD here again to trigger if above
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX PASSWORD CHANGE</title>
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
</style>
    <div class="content">
        <form action="user_pwd_change.php" method="POST">
            <?php
                if(!isset($_POST["id"]) || !isset($_SESSION["csrf"]) || !isset($_POST["sender"])){
                    echo("DATA NOT PRESENT IN REQUEST");
                    exit();
                }
              ?>
            <label for="mail">NEW PASSWORD:</label>
            <input type="password" name="pwd">
            <input type="hidden" name="csrf" value="<?PHP echo($_SESSION["csrf"]); ?>">
            <input type="hidden" name="id" value="<?PHP echo(htmlentities($_POST["id"])); ?>">
            <input type="hidden" name="sender" value="<?PHP echo(htmlentities($_POST["sender"])); ?>">
            <br>
            <button type="submit">CHANGE</button>
        </form>
    </div>
</body>
</html>