<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("./../login_check.php");
require_once("./../pdo.php");

if(!isset($_POST["record_id"]) || !isset($_POST["move"])){
    header("Location: ./../display.php?error=No_POST_data_detected");
    exit();
}

$itemId = htmlentities($_POST["record_id"]);
$direction = htmlentities($_POST["move"]);
echo($_POST["record_id"]."    ");
echo($_POST["move"]);

function moveItem(PDO $pdo, int $itemId, string $direction){
    $pdo->beginTransaction();

    try {
        // 1. Get current item
        $stmt = $pdo->prepare("SELECT id, order_number, channel_name FROM channels_config WHERE id = ?");
        $stmt->execute([$itemId]);
        $currentItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentItem) {
            echo "Item ID $itemId not found.<br>";
            $pdo->rollBack();
            return;
        }

        echo "Current Item: ID {$currentItem['id']}, Order: {$currentItem['order_number']}, Channel: {$currentItem['channel_name']}<br>";

        $orderNumber = $currentItem['order_number'];
        $channelName = $currentItem['channel_name'];

        // 2. Get swap item
        if ($direction === 'up') {
            $stmt = $pdo->prepare("
                SELECT id, order_number FROM channels_config
                WHERE order_number < ? AND channel_name = ?
                ORDER BY order_number DESC LIMIT 1
            ");
        } else {
            $stmt = $pdo->prepare("
                SELECT id, order_number FROM channels_config
                WHERE order_number > ? AND channel_name = ?
                ORDER BY order_number ASC LIMIT 1
            ");
        }

        $stmt->execute([$orderNumber, $channelName]);
        $swapItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$swapItem) {
            echo "No item to swap with in direction '$direction'.<br>";
            $pdo->rollBack();
            return;
        }

        echo "Swap Item: ID {$swapItem['id']}, Order: {$swapItem['order_number']}<br>";

        // 3. Do the swap using temporary order number
        $tempOrder = -1;

        $stmt = $pdo->prepare("UPDATE channels_config SET order_number = ? WHERE id = ?");
        $stmt->execute([$tempOrder, $currentItem['id']]);
        echo "Set current item to temp order<br>";

        $stmt->execute([$currentItem['order_number'], $swapItem['id']]);
        echo "Set swap item to current order<br>";

        $stmt->execute([$swapItem['order_number'], $currentItem['id']]);
        echo "Set current item to swap's order<br>";

        $pdo->commit();
        echo "Swap committed.<br>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Exception: " . $e->getMessage();
    }
}

try {
    moveItem($pdo, $itemId, $direction);
    echo "Move successful.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

header("Location: ./../display.php");
exit();

?>