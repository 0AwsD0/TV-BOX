<?php
if(isset($_POST["password"]) && isset($_POST["email"])){
    echo("Data processed! Admin account INSERT below: <br><br>");
    echo("INSERT INTO users(id,email,password,is_admin) VALUES(null,'".$_POST["email"]."','".password_hash($_POST["password"], PASSWORD_DEFAULT)."', 1);");
    //def admin admin -> INSERT INTO users(id,email,password,is_admin) VALUES(null,'admin','$2y$10$4XMgaHo6Yi/eDlwpIYYiIOYDv4oRIIMABD.v5REUMlQ9ih7UFRPUK', 1);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWD GEN</title>
    <link rel="icon" type="image/png" href="favicon.png">
</head>
<body>
    <h1>This file should be deleted after installation.</h1>
    <h3>It's used to create ADMIN user by insert query -> that you should paste into your database. It does not connect anywhere.</h3>
    <form action="" method="POST">
        <label for="email">Enter Email Address:</label>
        <input type="text" name="email" id="ml">
        <label for="password">Enter Password:</label>
        <input type="password" name="password" id="pwd">
        <button type="submit">Submit</button>
    </form>

</body>
</html>