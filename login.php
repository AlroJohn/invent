<?php
session_start();
include("conn.php"); // Database connection

header("Content-Type: application/json");

$contact = $_POST['contact'];  
$password = $_POST['password'];

// Query the database to check for user credentials
$query = "SELECT * FROM user WHERE phone = ? OR email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $contact, $contact);  
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password (without hashing)
    if (!empty($user['role']) && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; 
        
        // Update last activity timestamp
        $updateStmt = $conn->prepare("UPDATE user SET last_activity = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();
        
        // Redirect based on user role
        $redirectUrl = $user['role'] === 'admin' ? 'admin/index.php' : 'user/index.php';

        echo json_encode(["success" => true, "message" => "Login successful!", "redirect" => $redirectUrl]);
    } elseif (empty($user['role'])) {
        echo json_encode(["success" => false, "message" => "Account has no assigned role. Please contact support."]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found."]);
}

$stmt->close();
$conn->close();
?>
