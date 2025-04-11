<?php
include '../conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'];

$query = "DELETE FROM products WHERE id = $productId";
if ($conn->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting product.']);
}
?>
