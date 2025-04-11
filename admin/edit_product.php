<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);  // Enable error display

include '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log incoming POST data for debugging
    error_log(print_r($_POST, true));  // Check what data is being submitted

    // Validate required fields
    $requiredFields = ['product_id', 'product_name', 'price', 'stock', 'expiry'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => "Field {$field} is required."]);
            exit;
        }
    }

    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $expiry = $_POST['expiry'];

    // Check if expiration date is in the past
    if (strtotime($expiry) < strtotime(date('Y-m-d'))) {
        echo json_encode(['success' => false, 'message' => 'Expiration date cannot be in the past.']);
        exit;
    }

    // Retrieve the existing image path from the hidden input field
    $imagePath = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';

    // Handle new image upload if available
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        // Set the upload directory and new image name
        $uploadDir = '../uploads/';
        $imageName = basename($_FILES['product_image']['name']);
        $imagePath = $uploadDir . $imageName;  // Create path for the new image

        // Move the uploaded file to the desired directory
        if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $imagePath)) {
            echo json_encode(['success' => false, 'message' => 'Error uploading the image']);
            exit;
        }
    }

    // Prepare and execute the update query
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, expiration_date = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sdsssi", $name, $price, $stock, $expiry, $imagePath, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating product: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
