<?php
include '../conn.php';
// Get the product ID from the query parameter
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Debugging: Print the received ID
error_log("Received product ID: " . $productId); // Logs to the server's error log

if ($productId <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

// SQL query to fetch product details by ID
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);

// Bind the product ID to the SQL statement
$stmt->bind_param("i", $productId);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if a product was found
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode($product);
} else {
    echo json_encode(['error' => 'Product not found']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
