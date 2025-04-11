<?php
// Include your database connection file
include('../conn.php');

/**
 * Function to add a new user to the database
 *
 * @param string $username The username of the new user
 * @param string $email The email of the new user
 * @param string $role The role of the new user (admin, user, etc.)
 * @param string $password The password of the new user
 * @return string The result message (success or error)
 */
function addUser($username, $email, $role, $password) {
    global $conn;

    // Validate data
    if (empty($username) || empty($email) || empty($role)) {
        return "All fields are required!";
    }

    // Check if the email already exists
    $query = "SELECT * FROM user WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind the email parameter to the query
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any user already has this email
        if ($result->num_rows > 0) {
            $stmt->close();
            return "Email is already in use!";
        }
    } else {
        return "Error preparing the email check query: " . $conn->error;
    }

    // Hash the password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query for inserting the new user
    $query = "INSERT INTO user (username, email, role, password) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters to the SQL query
        $stmt->bind_param("ssss", $username, $email, $role, $hashedPassword);

        // Execute the query
        if ($stmt->execute()) {
            $stmt->close();
            return "User added successfully!";
        } else {
            $stmt->close();
            return "Error: " . $stmt->error;
        }
    } else {
        return "Error preparing the SQL query: " . $conn->error;
    }
}

/**
 * Function to delete a user from the database
 *
 * @param int $userId The ID of the user to delete
 * @return string The result message (success or error)
 */
function deleteUser($userId) {
    global $conn;

    // Prepare the delete query
    $query = "DELETE FROM user WHERE id = ?";

    if ($stmt = $conn->prepare($query)) {
        // Bind the user ID to the SQL query
        $stmt->bind_param("i", $userId);

        // Execute the delete query
        if ($stmt->execute()) {
            $stmt->close();
            return "User deleted successfully";
        } else {
            $stmt->close();
            return "Error: " . $stmt->error;
        }
    } else {
        return "Error preparing the SQL query: " . $conn->error;
    }
}

/**
 * Function to update a user's details
 *
 * @param int $userId The ID of the user to update
 * @param string $username The new username
 * @param string $email The new email
 * @param string $role The new role
 * @param string $password The new password (optional)
 * @return string The result message (success or error)
 */
function updateUser($userId, $username, $email, $role, $password = null) {
    global $conn;

    // If password is provided, hash it before saving
    if ($password) {
        $password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE user SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $username, $email, $role, $password, $userId);
    } else {
        // If password is not provided, update without changing the password
        $query = "UPDATE user SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $username, $email, $role, $userId);
    }

    // Execute the query
    if ($stmt->execute()) {
        $stmt->close();
        return "User updated successfully.";
    } else {
        $stmt->close();
        return "Error updating user: " . $stmt->error;
    }
}

// Check if the request is a POST request for adding or updating a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Determine the operation to perform
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];

        // Perform the appropriate operation based on the value of 'operation'
        if ($operation == 'add') {
            // Add user
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['password'];
            echo addUser($username, $email, $role, $password);
        } elseif ($operation == 'update') {
            // Update user
            $userId = $_POST['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $password = $_POST['password'] ?? null;
            echo updateUser($userId, $username, $email, $role, $password);
        }
    }
}

// Check if the request is a GET request for deleting a user
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    // Delete user
    $userId = $_GET['id'];
    echo deleteUser($userId);
}

?>
