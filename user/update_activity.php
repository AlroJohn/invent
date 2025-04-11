<?php
session_start();
include('../conn.php');

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE user SET last_activity = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
?>
