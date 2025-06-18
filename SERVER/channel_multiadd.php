<?php
require("./login_check.php");
require_once("./pdo.php");

if(isset($_GET['status']) && $_GET['status'] == 'ERROR'){
        echo('<h1 style="color: white; background-color: red; padding: 20px;">ERROR - channel_multiadd_action.php</h1>');
}

$preselectedChannels = $_GET['channel'] ?? []; // will be an array if passed like channel[]=A&channel[]=B

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX MULTIADD</title>
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
<main>
<?php require('./elements/side_bar.php'); ?>

<div id="content">
    <div class="row">
        <div class="col-12">
            <div id="toggle">
                <div id="toggle-menu" onclick="menu_toggle()"><i class="fa-solid fa-bars"></i></div>
                <p id="tip"> Menu </p>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col info-col p-0" style="background-color: #404040; border-radius: 15px;"></div>
    </div>

    <!-- SELECT CHANNELS -->
    <form id="channelForm">
        <div class="row" style="color: white; background-color: #404040; padding: 1em; border-radius: 20px;">
            <h1>SELECT CHANNELS</h1>
            <?php
                $sql = "SELECT name FROM channels;";
                $result = $pdo->query($sql);
                $names = $result->fetchAll(PDO::FETCH_COLUMN);
                foreach($names as $name){
                    $checked = in_array($name, $preselectedChannels) ? 'checked' : '';
                    echo('<div style="width: auto; float: left; font-size: 20px; font-weight: bold; padding: 10px; padding-left: 20px; padding-right: 20px; margin-top: 10px; margin-right: 10px; background-color: black; border-radius: 50px / 100%;">
                            <input type="checkbox" style="width: 20px; height: 20px; cursor: pointer;" name="channel[]" value="'.htmlspecialchars($name).'" '.$checked.'>&nbsp;'.htmlspecialchars($name).'
                        </div>');
                }
            ?>
               </form>
        </div>

<form id="saveForm" method="POST" action="./channel_multisave.php" onsubmit="return prepareSaveForm();">
  <div class="row">
        <div id="saveFormChannels"></div>
            <div class="col text-center">
            <div style="display: inline-block;"><button type="submit" class="display-btn" style="opacity: 0.75; border-radius: 0px 0px 20px 20px;">&nbsp;SAVE Selected &nbsp;</button></div>
            </div>
  </div>
</form>

    <!-- DISPLAY MEDIA FILES -->
    <div class="row mt-4">
        <?php
        $dir = './media';
        $files = scandir($dir, 0);
        for($i = 2; $i < count($files); $i++) {
            $filename = htmlspecialchars($files[$i]);
            $filepath = './media/' . $filename;
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($extension, ['jpg', 'mp4'])) continue;
        ?>
            <div class="col-lg-3 mb-3" style="background-color: #404040; border: 10px solid #AEAEAE; box-sizing: border-box; overflow: hidden; border-radius: 20px; position: relative;">

                <?php if ($extension === 'jpg'): ?>
                    <img class="media-img" src="<?= $filepath ?>" onclick="media_tails_in(this)">
                <?php else: ?>
                    <video class="media-video" preload="none" controls src="<?= $filepath ?>"></video>
                <?php endif; ?>

                <p class="media-p text-center"><?= $filename ?></p>

                <!-- INDIVIDUAL FORM PER FILE -->
                <form method="POST" action="./channel_multiadd_action.php" onsubmit="return attachSelectedChannels(this);">
                    <input type="hidden" name="file" value="<?= $filename ?>">
                    <div class="channels-container"></div>
                    <button type="submit" class="file mt-2"><i class="fa-solid fa-plus"></i> ADD</button>
                </form>
            </div>
        <?php } ?>
    </div>
</div>
</main>

<!-- JAVASCRIPT TO COPY SELECTED CHANNELS -->
<script>
function getSelectedChannels() {
    return document.querySelectorAll('#channelForm input[type="checkbox"]:checked');
}

function attachSelectedChannels(form) {
    const selected = getSelectedChannels();
    const container = form.querySelector('.channels-container');
    container.innerHTML = '';

    if (selected.length === 0) {
        alert("Please select at least one channel.");
        return false;
    }

    selected.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'channel[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });

    return true;
}

function prepareSaveForm() {
    const selected = getSelectedChannels();
    const container = document.getElementById('saveFormChannels');
    container.innerHTML = '';

    if (selected.length === 0) {
        alert("Please select at least one channel.");
        return false;
    }

    selected.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'channel[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });

    return true;
}
</script>
</body>
</html>
