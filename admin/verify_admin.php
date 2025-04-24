<?php
session_start();
include("../conn.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the JSON data from the request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Debug - log the received data
error_log('Received data: ' . print_r($data, true));

if (!isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// Get the current user's ID and check if they're an admin
$userId = $_SESSION['user_id'];

// Debug - log the session data
error_log('Session data: ' . print_r($_SESSION, true));

$userQuery = "SELECT * FROM user WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Verify the provided password against the admin's password
$admin = $result->fetch_assoc();

// Debug - log the password comparison (remove in production!)
error_log('Comparing passwords: ' . $data['password'] . ' vs ' . $admin['password']);

if ($data['password'] === $admin['password']) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
}
?>