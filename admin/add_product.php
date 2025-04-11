<?php
$response = ['success' => false, 'message' => 'Something went wrong'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include '../conn.php'; // Include your database connection

        // Validate required fields
        $requiredFields = ['product_name', 'price', 'stock', 'expire'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field {$field} is required.");
            }
        }

        $name = trim($_POST['product_name']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $expire = trim($_POST['expire']);

        if ($price < 0 || $stock < 0) {
            throw new Exception('Price and stock must be positive values.');
        }

        $imagePath = null;

        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['product_image']['tmp_name'];
            $fileName = basename($_FILES['product_image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid image format. Allowed formats: ' . implode(', ', $allowedExtensions));
            }

            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                throw new Exception('Upload directory does not exist.');
            }

            $newFileName = uniqid('img_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                throw new Exception('Error saving the image.');
            }

            $imagePath = $destPath;
        }

        // Insert product data into the database
        $query = "INSERT INTO products (name, price, stock, expiration_date, image_path) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sdiss', $name, $price, $stock, $expire, $imagePath);

        if (!$stmt->execute()) {
            throw new Exception('Database error: ' . $stmt->error);
        }

        $response['success'] = true;
        $response['message'] = 'Product added successfully';

        $stmt->close();
        $conn->close();
    } else {
        throw new Exception('Invalid request method.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
