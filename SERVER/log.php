<?php
//date("Y-m-d H:i:s") [TYPE] [Subsystem] Message.
//TYPE-> [ERROR] [WARN] [INFO] [DEBUG]
function logEvent($type, $message){
    if(is_string($type) && is_string($message)){
        switch($type){
            case "error":
                $type = "[ERROR] ";
                break;
            case "warning":
                $type = "[WARNING] ";
                break;
            case "info":
                $type = "[INFO] ";
                break;
            default:
                $type = "[DEBUG] ";
                break;
        }
        $line = date("Y-m-d H:i:s ").$type.$message."\n";
        $file = "./log/".date('Y_m_d').".log";
        file_put_contents($file, $line, FILE_APPEND);
    }
    else{
        $file = "./log/".date('Y:m:d').".log";
        $line = date("Y-m-d H:i:s")." [ERROR] [LOG] One of provided variable is not a string type!";
        file_put_contents($file, $line, FILE_APPEND);
    }
}
?>