<?php
include("conn.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize the inputs
    $customer_no = trim($_POST['customer_no']); // Optional
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']); // Optional
    $last_name = trim($_POST['last_name']);
    $suffix = trim($_POST['suffix']); // Optional
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Initialize response array
    $response = [];

    // Validation checks
    if (empty($customer_no) || empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($username) || empty($password)) {
        $response = ["success" => false, "message" => "Please fill in all required fields."];
        echo json_encode($response);
        exit;
    }

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ["success" => false, "message" => "Please enter a valid email address."];
        echo json_encode($response);
        exit;
    }

    // Password length check
    if (strlen($password) < 6) {
        $response = ["success" => false, "message" => "Password must be at least 6 characters."];
        echo json_encode($response);
        exit;
    }

    // Check if the username or email already exists in the user table
    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If there's a match, show an error
        $response = ["success" => false, "message" => "Username or email already exists."];
        echo json_encode($response);
        exit;
    }

    // Check if the email or phone already exists in the customers table
    $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If there's a match, show an error
        $response = ["success" => false, "message" => "Email or phone number already exists."];
        echo json_encode($response);
        exit;
    }

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Insert into the user table (role as 'customer')
        $stmt = $conn->prepare("INSERT INTO user (username, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->bind_param("ssss", $username, $email, $phone, $password);
        $stmt->execute();

        // Get the last inserted ID
        $userid = $conn->insert_id;

        // Insert into the customers table
        $stmt = $conn->prepare("INSERT INTO customers (customer_no, first_name, middle_name, last_name, suffix, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $customer_no, $first_name, $middle_name, $last_name, $suffix, $email, $phone);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Respond with success
        $response = ["success" => true, "message" => "Registration successful! Redirecting to homepage..."];
        echo json_encode($response);
    } catch (mysqli_sql_exception $e) {
        // If an error occurs, roll back the transaction
        $conn->rollback();
        $response = ["success" => false, "message" => "Error: " . $e->getMessage()];
        echo json_encode($response);
    }

} else {
    // If the request method is not POST
    $response = ["success" => false, "message" => "Invalid request method"];
    echo json_encode($response);
}
?>