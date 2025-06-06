<?php
require_once("./log.php");
require("./pdo.php");

// BRUTE-FORCE CONFIGURATION
define('MAX_ATTEMPTS', 5);
define('BAN_TIME', 600); // 10 minutes in seconds

session_start();
//if logged in than redirect
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
    header("Location: index.php");
    exit();
}

//CSRF TOKEN
// if(!isset($_POST['csrf']) || $_POST['csrf'] == ''){
//     $token = bin2hex(random_bytes(32));
//     $SESSION['csrf'] = $token;
// }

if(isset($_SESSION['login_attempt'])){
    unset($_SESSION['login_attempt']);
        // $currentTime = time(); // Uncomment this block to double count failed attempts while login is valid
        // $attempt_query = "INSERT INTO `login_attempts` (`ip_address`, `timestamp`) VALUES (:ip_address, :timestamp)";
        // $sql = $pdo->prepare($attempt_query);
        // $sql->execute([
        //     ':ip_address' => $_SERVER['REMOTE_ADDR'],
        //     ':timestamp' => $currentTime
        // ]);
    echo('<div id="login-failed">Login failed. Enter valid credentials.</div>'); //when uname is valid but pwd not
}

if(!isset($_POST['pwd']) || !isset($_POST['mail']) || !isset($_POST['csrf'])){
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf'] = $token;
}


if(isset($_POST['pwd']) && isset($_POST['mail']) && isset($_POST['csrf'])){
    //print_r('<BR>');
    //print_r($_POST['csrf']);
    //print_r('<BR>');
    //print_r($_SESSION['csrf']);
    //print_r('<BR>');

    if($_POST['csrf'] != $_SESSION['csrf']){
        logEvent("error", "[LOGIN] Login failed !!!CSRF!!!. FROM: ".$_SERVER['REMOTE_ADDR']);
        echo('<h1>!!!CSRF!!! ERROR - Try again / check your security</h1>');
        exit();
    }

    //SECTION BRUTE-FORCE
    $ip = $_SERVER['REMOTE_ADDR'];
    $currentTime = time();

    // 1. DELETE OLD ATTEMPTS (older than 10 min)
    $cleanupStmt = $pdo->prepare("DELETE FROM login_attempts WHERE timestamp < :time_limit");
    $cleanupStmt->execute([':time_limit' => $currentTime - BAN_TIME]);

    // 2. COUNT RECENT ATTEMPTS FROM THIS IP
    $attemptsStmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = :ip AND timestamp >= :time_limit");
    $attemptsStmt->execute([
        ':ip' => $ip,
        ':time_limit' => $currentTime - BAN_TIME
    ]);

    $attempts = $attemptsStmt->fetchColumn();

    if ($attempts >= MAX_ATTEMPTS) {
        logEvent("DEBUG", "[LOGIN] Login failed - IP BLOCKED. ".$_SERVER['REMOTE_ADDR']);
        echo("<h1>Too many failed login attempts. Please try again later.</h1>");
        //exit("Too many failed login attempts. Please try again later.");
        exit();
    }
    //SECTION BRUTE-FORCE END


    $sql = "SELECT * FROM users WHERE users.email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $_POST['mail']]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$result){
            $currentTime = time();
            $attempt_query = "INSERT INTO `login_attempts` (`ip_address`, `timestamp`) VALUES (:ip_address, :timestamp)";
            $stmt = $pdo->prepare($attempt_query);
            $stmt->execute([
                ':ip_address' => $_SERVER['REMOTE_ADDR'],
                ':timestamp' => $currentTime
            ]);
        echo('<div id="login-failed">Login failed. Enter valid credentials.</div>');
        logEvent("warning", "[LOGIN] Login failed - invalid username. FROM: ".$_SERVER['REMOTE_ADDR']);
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf'] = $token;
    }

    foreach($result as $row){



        if (password_verify($_POST['pwd'], $row["password"])) {
            //print_r("<br>");
            echo('Password is valid!');
            logEvent("info", "[LOGIN] > ".$_POST["mail"]." Logged in  FROM: ".$_SERVER['REMOTE_ADDR']);
            //$_POST['csrf'] == '';
        } else {
                $currentTime = time();
                $attempt_query = "INSERT INTO `login_attempts` (`ip_address`, `timestamp`) VALUES (:ip_address, :timestamp)";
                $stmt = $pdo->prepare($attempt_query);
                $stmt->execute([
                    ':ip_address' => $_SERVER['REMOTE_ADDR'],
                    ':timestamp' => $currentTime
                ]);
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf'] = $token;
            $_SESSION['login_attempt'] = "Invalid password."; //uname provided and valid
            logEvent("warning", "[LOGIN] Login failed - incorrect password. FROM: ".$_SERVER['REMOTE_ADDR']);
            echo('<div id="login-failed">Login failed. Enter valid credentials.</div>');
            header("Location: login.php");
            exit();
        }

        //create variable session logged_in == true
        //create variable session -> email / to display on left nav in panel | others are important too
            $_SESSION['email'] = $row["email"];
            $_SESSION["privileges"] = $row["is_admin"];
            $_SESSION['user_id'] = $row["id"];
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit();

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX LOGIN</title>
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body style="background-color: #313131; font-family: 'Ubuntu', sans-serif;">
<style>
.content{
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
        <form action="login.php" method="POST">
            <label for="mail">Email:</label>
            <input type="text" name="mail">
            <label for="pwd">Password:</label>
            <input type="password" name="pwd">
            <input type="hidden" name="csrf" value="<?PHP echo($token); ?>">
            <br>
            <button type="submit" name="submit">LOGIN</button>
        </form>
    </div>
</body>
</html>