<?php
require("./login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX ABOUT</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="description" content="TVBOX - a management system for your TV resources.">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="fontawesome/js/all.min.js"></script>
    <script src="js/main.js" defer></script>
</head>
<body>
<!-- SIDE BAR -->
<main>
<?php
    require('./elements/side_bar.php');
    require('./pdo.php');
?>
<!-- /SIDE BAR -->
    <div id="content">
       <div class="row">
            <div class="col-12">
                <div id="toggle"><div id="toggle-menu" onclick="menu_toggle()"><i class="fa-solid fa-bars"></i></div> <p id="tip"> Menu </p></div>
            </div>
            <div class="col-12">
                <div class="row channel">
                    <div class="col-12 text-center">
                        <h1>About</h1>
                        <br>
                        <b>Github repository: https://github.com/0AwsD0/TV-BOX</b> <br><br>
                        <p>Documentation uses 'MkDocs' with 'Material' theme: <br> <a href="https://github.com/mkdocs/mkdocs" style="cursor: pointer;">https://github.com/mkdocs/mkdocs</a> <br> <a href="https://github.com/squidfunk/mkdocs-material" style="cursor: pointer;">https://github.com/squidfunk/mkdocs-material</a></p>
                        <p>One PHP file makes use of 'getID3' - <a href="https://github.com/JamesHeinrich/getID3" style="cursor: pointer;">https://github.com/JamesHeinrich/getID3</a></p>
                        <p>Frontend uses 'Bootstrap' - <a href="https://getbootstrap.com/" style="cursor: pointer;">https://getbootstrap.com/</a></p>
                        <p>Frontend uses 'Fontawesome' - <a href="https://fontawesome.com/" style="cursor: pointer;">https://fontawesome.com/</a></p>
						<br>
                        <p>The full documentation of the project can be found in /tv-box-doc/site</p>
                    </div>
                </div>
            </div>
       </div>
    </div>
</main>

</body>
</html>