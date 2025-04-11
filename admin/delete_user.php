<?php
// Include your database connection file
include('../conn.php');

// Check if the user ID is provided via GET
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare the delete query
    $query = "DELETE FROM user WHERE id = ?";

    if ($stmt = $conn->prepare($query)) {
        // Bind the user ID to the SQL query
        $stmt->bind_param("i", $userId);

        // Execute the delete query
        if ($stmt->execute()) {
            // Redirect to the user list page after successful deletion
            header("Location: users.php?message=User deleted successfully");
        } else {
            // If there is an error with deletion
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing the SQL query: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect to user list if no ID is specified
    header("Location: users.php");
}
?>
