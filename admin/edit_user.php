<?php
include('../conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the data from the AJAX request
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $newPassword = $_POST['password'];
    $oldPassword = $_POST['old_password'];

    // If new password is provided, verify the old password
    if (!empty($newPassword)) {
        // Check if old password is provided
        if (empty($oldPassword)) {
            echo "Current password is required to set a new password.";
            exit;
        }

        // Verify the old password
        $verifyQuery = "SELECT password FROM user WHERE id = ?";
        $verifyStmt = $conn->prepare($verifyQuery);
        $verifyStmt->bind_param("i", $userId);
        $verifyStmt->execute();
        $result = $verifyStmt->get_result();

        if ($result->num_rows === 0) {
            echo "User not found.";
            exit;
        }

        $userData = $result->fetch_assoc();
        $storedPassword = $userData['password'];

        // Compare the provided old password with the stored password
        if ($oldPassword != $storedPassword) {
            echo "Current password is incorrect.";
            exit;
        }

        // Old password verified, proceed with update
        // For future security, consider using password_hash() and password_verify()
        $password = $newPassword; // No hashing for now to match your current code
    } else {
        // If password is not provided, don't change it
        $password = null;
    }

    // Prepare the SQL query
    if ($password) {
        // Update with the new password
        $query = "UPDATE user SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $email, $role, $password, $userId);
    } else {
        // Update without changing password
        $query = "UPDATE user SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $email, $role, $userId);
    }

    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Error updating user: " . $stmt->error;
    }

    $stmt->close();
}
?>