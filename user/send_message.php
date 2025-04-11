<?php
session_start();
include("../conn.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id']; 
$message = $_POST['message'];

$sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error sending message"]);
}

$stmt->close();
$conn->close();
?>
