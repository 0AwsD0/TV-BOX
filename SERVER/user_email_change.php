<?php
require("./login_check.php");

require_once("./log.php");

require("./pdo.php");

if(isset($_POST["email"]) && isset($_POST["csrf"])){

    if($_POST["email"] == ''){
        logEvent("warning", "[EMAIL CHANGE] Email empty - BY: ".$_SESSION["email"]);
        header("Location: settings.php?user=EMAIL_CHANGE_FAILED");
        exit();
    }

    if(strlen($_POST["email"]) < 6 ){ // a@b.cd -> 6 CHAR
        logEvent("warning", "[EMAIL CHANGE] Email invalid - BY: ".$_SESSION["email"]);
        header("Location: settings.php?user=EMAIL_CHANGE_FAILED (NOT A VALID EMAIL ADDRESS)");
        exit();
    }

    if($_POST["csrf"] == $_SESSION["csrf"]){
        $sql = 'UPDATE users SET email = :email WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $_POST['email'],
            ':id' => $_SESSION['user_id']
        ]);
        logEvent("info", "[EMAIL CHANGE] Email changed - BY: ".$_SESSION["email"]);
        header("Location: settings.php?user=EMAIL_CHANGE_SUCCESS");
        exit();
    }
    else{
        logEvent("warERRORning", "[EMAIL CHANGE] Email invalid - BY: ".$_SESSION["email"]);
        header("Location: settings.php?user=CSRF_FAIL");
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX EMAIL CHANGE</title>
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
    width: 100% ;
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
        <form action="user_email_change.php" method="POST">
            <?php
                if(!isset($_SESSION["csrf"])){
                    echo("DATA NOT PRESENT IN REQUEST");
                    exit();
                }
              ?>
            <label for="mail">NEW EMAIL:</label>
            <input type="text" name="email">
            <input type="hidden" name="csrf" value="<?PHP echo(htmlentities($_SESSION["csrf"])); ?>">
            <br>
            <button type="submit">CHANGE</button>
        </form>
    </div>
</body>
</html>