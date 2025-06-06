<?php
require("./login_check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV-BOX MEDIA</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <meta name="description" content="TVBOX, remote display system zdalnego zarządzania zasobami TV.">
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
?>
<!-- /SIDE BAR -->
 <div id="content">

        <div id="preview-window" onclick="media_tails_out()">
           <div id="close-preview">CLOSE PREVIEW (X)</div>
           <img id="preview-img" src="" alt="local clicked image">
       </div>

       <div class="row">
            <div class="col-12">
                <div id="toggle"><div id="toggle-menu" onclick="menu_toggle()"><i class="fa-solid fa-bars"></i></div> <p id="tip"> Menu </p></div>
            </div>
       </div>

       <div class="row info">
        <div class="col-lg-12 col-xl-8 info-col">
            <p>UPLOAD Files</p>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                Select files to upload:
                <input type="file" name="upload[]" multiple class="file-button">
                <input type="submit" value="Upload Files" name="submit" class="file-button" id="file-submit">
                    <?php
                        if(isset($_GET["upload"]) && $_GET["upload"] == "SUCCESS"){
                            echo('<p style="color: #00ff70">Upload SUCCESS!</p>');
                        }
                        else if(isset($_GET["upload"]) && $_GET["upload"] == "FAIL"){
                            echo('<p style="color: #f00">Upload FAIL!</p>');
                        }
                        else if(isset($_GET["upload"]) && $_GET["upload"] == "EMPTY"){
                            echo('<p style="color: #fffb00">SELECT FILES FIRST</p>');
                        }
                        else if(isset($_GET["upload"]) && $_GET["upload"] == "FORMAT"){
                            echo('<p style="color: #fffb00">ONE OR MORE FILES IN WRONG FORMAT</p>');
                        }
                    ?>
            </form>
        </div>
        <div class="col-lg-6 col-xl-4 info-col">
            <p>[SPACE TAKEN]</p>
            <p class="info-col-p">
                <?php
                //booth functions procured using Sir "Copy's Paste's" method -> from stack overflow
                function GetDirectorySize($path){
                        $bytestotal = 0;
                        $path = realpath($path);
                        if($path!==false && $path!='' && file_exists($path)){
                            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                                $bytestotal += $object->getSize();
                            }
                        }
                        return $bytestotal;
                    }
                function formatBytes($bytes, $precision = 2) {
                        $units = array('B', 'KB', 'MB', 'GB', 'TB');

                        $bytes = max($bytes, 0);
                        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                        $pow = min($pow, count($units) - 1);

                        // Uncomment one of the following alternatives
                         $bytes /= pow(1024, $pow);
                        // $bytes /= (1 << (10 * $pow));

                        return round($bytes, $precision) . $units[$pow];
                    }
                    $media_bytes = GetDirectorySize("./media");
                    $media_size = formatBytes($media_bytes);
                    echo(htmlspecialchars($media_size));
                ?>
            </p>
        </div>
    </div>

        <!-- Below is the spacer -->
        <div class="col-lg-12 col-xl-6 info-col">
        </div>

       <div class="row">
       <?php
        $dir = './media';
        $files = scandir($dir, 0);
        $count = 0;
        for($i = 2; $i < count($files); $i++)
        {
            /*if($count == 0 || $count%5 == 0 ){
                echo('<div class="row">');
                echo('IF COUNT1 <--'.$count.' |');
             }*/
            //print $files[$i]."<br>"; // '.' explode -> if jpg if .mp4
            $file_name = explode(".", $files[$i])[0]; // '.' explode -> if jpg if .mp4
            $file_extension = explode(".", $files[$i])[1]; // '.' explode -> if jpg if .mp4

            if($file_extension == "jpg"){
                echo('<div class="col-lg-3" style="background-color: #404040; border: 10px solid #AEAEAE; box-sizing: border-box; overflow: hidden; border-radius: 20px; position: relative;"><img class="media-img" src="./media/'.htmlspecialchars($files[$i]).'" onclick="media_tails_in(this)"><p class="media-p text-center">'.htmlspecialchars($files[$i]).'</p><form action="./file_delete.php" method="post"> <input type="hidden" name="file" value="'.htmlspecialchars($files[$i]).'"><button class="file" type="submit"><i class="fa-solid fa-trash"></i> DELETE</button></form></div>');
            }
            else if($file_extension == "mp4"){
                echo('<div class="col-lg-3" style="background-color: #404040; border: 10px solid #AEAEAE; box-sizing: border-box; overflow: hidden; border-radius: 20px; position: relative;"><video class="media-video" preload="none" controls src="./media/'.htmlspecialchars($files[$i]).'"></video><p class="media-p text-center">'.htmlspecialchars($files[$i]).'</p><form action="./file_delete.php" method="post"> <input type="hidden" name="file" value="'.htmlspecialchars($files[$i]).'"><button class="file" type="submit"><i class="fa-solid fa-trash"></i> DELETE</button></form></div>');
            }

          /*  if($count%4 == 0 && $count != 0){
                echo('</div>');
                echo('IF COUNT2 <--'.$count.' |');
            }
*/
            $count++;

        }
       ?>
       </div>

  </div>
</main>

</body>
</html>