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
   // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query for inserting the new user
    $query = "INSERT INTO user (username, email, role, password) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters to the SQL query
        $stmt->bind_param("ssss", $username, $email, $role, $password);

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

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Call the addUser function
    $result = addUser($username, $email, $role, $password);

    // Output the result message
    echo $result;
}
?>
