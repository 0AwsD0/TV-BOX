<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TV-BOX UPLOADING</title>
  <link rel="stylesheet" href="fontawesome/css/all.min.css">
</head>
<body style="background-color: #313131;">
<div class="content"><h1 style="font-family: 'Ubuntu', sans-serif;">PLEASE WAIT - UPLOAD IN PROGRESS <i class="fa-solid fa-gear" class="rotating" id="loading"></i></h1></div>
<style>
.content {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    color: white;
  }
  @-webkit-keyframes rotating {
      from{
          -webkit-transform: rotate(0deg);
      }
      to{
          -webkit-transform: rotate(360deg);
      }
  }
  @keyframes rotating {
      from{
          -webkit-transform: rotate(0deg);
      }
      to{
          -webkit-transform: rotate(360deg);
      }
  }
  #loading{
    animation: rotating 2s linear infinite;
    -webkit-animation: rotating 2s linear infinite;
  }
</style>
</body>
</html>
<?php
require("./login_check.php");
require_once("./log.php");

//below could be useful - send in array by js/post to make multiple request and allow drag and drop setup
//$files = array_filter($_FILES['upload']['name']); //something like that to be used before processing files.

// Count # of uploaded files in array
if(!isset($_FILES['upload']['name'])){
  echo('<h2 style="color: #555555;">FILES NOT DETECTED</h2>');
  exit();
}

$total = count($_FILES['upload']['name']);

//echo("TOTAL".$total);
//print_r($_FILES);

$flag = 0; //if 1 -> some files ware not uploaded -> wrong extension

//to do -> check extensions only to accept mp4 and jpg files
// Loop through each file
for( $i=0 ; $i < $total ; $i++ ) {
  //Get the temp file path
  $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

  //Informs user when no files ware selected yet upload button was pressed
  if(empty($_FILES['upload']['name'][0])){
    logEvent("info", "[UPLOAD] Upload failed! <Empty> BY: ".$_SESSION["email"]);
    header("Location: media.php?upload=EMPTY");
    exit();
  }

  $extension = explode(".",$_FILES['upload']['name'][$i]);
  if($extension[1] == "mp4" || $extension[1] == "jpg") //this prevents upload of any file other than mp4 and jpg
  {
    //Make sure we have a file path
    if ($tmpFilePath != ""){
    //Setup our new file path
    $uploadName = preg_replace('/\s+/', '_', $_FILES['upload']['name'][$i]);
    $newFilePath = "./media/".$uploadName;
    //Upload the file into the temp dir
      if(move_uploaded_file($tmpFilePath, $newFilePath)) {
        //Handle other code here
        $_POST["upload_return"] = "SUCCESS";
        logEvent("debug", "[UPLOAD] ".$_FILES['upload']['name'][$i]." - Uploaded! BY: ".$_SESSION["email"]);
        echo("Upload success. <br>");
      }
      else{
          logEvent("error", "[UPLOAD] Upload failed! BY: ".$_SESSION["email"]);
          header("Location: media.php?upload=FAIL");
          exit();
      }
    }
  }
  else{
        $flag = 1;
        logEvent("error", "[UPLOAD] ".$_FILES['upload']['name'][$i]." is not mp4/jpg FILE! - NOT Uploaded! BY: ".$_SESSION["email"]);
  }

}

echo("Loop done!");

if($flag == 1){
  logEvent("warning", "[UPLOAD] One or more files ware in wrong format - Upload attempt BY: ".$_SESSION["email"]);
  header("Location: media.php?upload=FORMAT");
  exit();
}

logEvent("info", "[UPLOAD] Upload SUCCESS! BY: ".$_SESSION["email"]);
header("Location: media.php?upload=SUCCESS");
exit();

//maybe to do -> for preload="none" usage grab some frame from video if possible and make as thumbnail -> poster="img/cover.jpg"

/*
This snippet is better isn't it?

$total = count($_FILES['upload']['name']);
$allowedExtensions = ['mp4', 'jpg'];
$allowedMimes = ['video/mp4', 'image/jpeg'];
$maxFileSize = 50 * 1024 * 1024; // 50MB
$flag = 0;

for ($i = 0; $i < $total; $i++) {
    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

    if (empty($_FILES['upload']['name'][$i]) || $tmpFilePath == '') {
        logEvent("info", "[UPLOAD] Upload failed! <Empty> BY: " . $_SESSION["email"]);
        header("Location: media.php?upload=EMPTY");
        exit();
    }

    $ext = strtolower(pathinfo($_FILES['upload']['name'][$i], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExtensions)) {
        $flag = 1;
        logEvent("error", "[UPLOAD] " . $_FILES['upload']['name'][$i] . " wrong extension - NOT Uploaded! BY: " . $_SESSION["email"]);
        continue;
    }

    if ($_FILES['upload']['size'][$i] > $maxFileSize) {
        $flag = 1;
        logEvent("error", "[UPLOAD] " . $_FILES['upload']['name'][$i] . " too large - NOT Uploaded! BY: " . $_SESSION["email"]);
        continue;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpFilePath);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimes)) {
        $flag = 1;
        logEvent("error", "[UPLOAD] " . $_FILES['upload']['name'][$i] . " wrong MIME type ($mime) - NOT Uploaded! BY: " . $_SESSION["email"]);
        continue;
    }

    $uploadName = uniqid() . '.' . $ext; // unique name to avoid overwriting

    $newFilePath = "./media/" . $uploadName;

    if (!move_uploaded_file($tmpFilePath, $newFilePath)) {
        logEvent("error", "[UPLOAD] Upload failed for " . $_FILES['upload']['name'][$i] . " BY: " . $_SESSION["email"]);
        header("Location: media.php?upload=FAIL");
        exit();
    }

    logEvent("debug", "[UPLOAD] " . $_FILES['upload']['name'][$i] . " uploaded as " . $uploadName . " BY: " . $_SESSION["email"]);
}

if ($flag === 1) {
    logEvent("warning", "[UPLOAD] One or more files were rejected due to format or size BY: " . $_SESSION["email"]);
    header("Location: media.php?upload=FORMAT");
    exit();
}

logEvent("info", "[UPLOAD] Upload SUCCESS! BY: " . $_SESSION["email"]);
header("Location: media.php?upload=SUCCESS");
exit();

*/

?>