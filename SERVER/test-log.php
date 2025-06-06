<?php
require("./login_check.php");

if(isset($_SESSION['privileges']) || $_SESSION['privileges'] != 1){
    echo('RESTRICTED ACCESS');
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Viewer</title>
    <style>
        body {
            font-family: monospace;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .log-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .log-line {
            border-bottom: 1px solid #eee;
            padding: 4px 0;
        }
        .INFO { color: green; }
        .ERROR { color: red; }
        .DEBUG { color: gray; }
        .WARN { color: orange; }
    </style>
</head>
<body>
    <h1>Log File Viewer</h1>
    <div class="log-container">
        <?php
        $logFile = 'example_log_file.log'; // Ensure this file is in the same directory as the script

        if (file_exists($logFile)) {
            $lines = file($logFile);
            foreach ($lines as $line) {
                // Extract log level
                preg_match('/\[(INFO|ERROR|DEBUG|WARN)\]/', $line, $matches);
                $level = $matches[1] ?? 'INFO';
                echo "<div class='log-line $level'>" . htmlspecialchars($line) . "</div>";
            }
        } else {
            echo "<p style='color: red;'>Log file not found.</p>";
        }
        ?>
    </div>
</body>
</html>