<?php
include('../conn.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the data from the AJAX request
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // If password is provided, hash it before saving
    if (!empty($password)) {
       // $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If password is not provided, keep the current password (you can query the current password from DB if needed)
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
