<?php
include('../conn.php');
session_start(); // Ensure session is started

$current_user_id = $_SESSION['user_id']; // Get the logged-in user's ID

$result = $conn->query("SELECT id, username, last_activity FROM user WHERE id != $current_user_id");

$users = [];
$current_time = time();

while ($user = $result->fetch_assoc()) {
    $last_activity = strtotime($user['last_activity']);
    $time_diff = $current_time - $last_activity;
    
    if ($time_diff < 300) { // Online if last activity within 5 minutes
        $status = 'Online';
        $last_seen = '';
    } else {
        $status = 'Offline';
        $last_seen = date("M d, Y h:i A", $last_activity);
    }

    $users[] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'status' => $status,
        'last_seen' => $last_seen
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'users' => $users]);
?>
