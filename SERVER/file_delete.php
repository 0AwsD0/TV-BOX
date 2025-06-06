<?php
require("./login_check.php");
require_once("./log.php");

$mediaDir = realpath(__DIR__ . '/media');
$file = $_POST['file'] ?? '';

// Resolve the real absolute path of the file
$targetPath = realpath($mediaDir . DIRECTORY_SEPARATOR . $file);

// Check that the file exists and is inside the allowed directory
if ($targetPath !== false && str_starts_with($targetPath, $mediaDir) && file_exists($targetPath)) {
    unlink($targetPath);
    echo "File deleted.";
} else {
    echo "Invalid file path.";
}

header('Location: media.php');
exit();

/*
This is propably better:

<?php
require("./login_check.php");
require_once("./log.php");

// Define the allowed directory (absolute path)
$mediaDirectory = realpath(__DIR__ . '/media'); // change if your files are elsewhere

if (isset($_POST['file'])) {
    $file = $_POST['file'];

    // Resolve the full absolute path
    $targetPath = realpath($mediaDirectory . DIRECTORY_SEPARATOR . $file);

    // Check if the file is actually inside the media directory
    if ($targetPath !== false && str_starts_with($targetPath, $mediaDirectory) && file_exists($targetPath)) {
        if (unlink($targetPath)) {
            logEvent("info", "[FILE DELETE] Success! - " . $targetPath);
            echo 'File deleted successfully.';
        } else {
            logEvent("error", "[FILE DELETE] Failed to delete file - " . $targetPath);
            echo 'Failed to delete file.';
        }
    } else {
        logEvent("warning", "[FILE DELETE] Invalid or unsafe file path - " . htmlspecialchars($file));
        echo 'Invalid file or path.';
    }
}

header('Location: media.php');
exit();
?>
*/

?>

