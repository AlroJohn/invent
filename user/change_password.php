<?php
session_start();
include("../conn.php");

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You are not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Fetch user's current password from DB
$query = "SELECT password FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($stored_password);
$stmt->fetch();
$stmt->close();

// Verify current password (without hash)
if ($current_password !== $stored_password) {
    echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
    exit();
}

// Update password in database (no hashing)
$update_query = "UPDATE user SET password = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $new_password, $user_id);

if ($update_stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Password updated successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating password."]);
}

$update_stmt->close();
$conn->close();
?>
