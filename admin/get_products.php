<?php
include '../conn.php';

$query = "SELECT id, name, price, stock, image_path FROM products";
$result = $conn->query($query);

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$conn->close();

echo json_encode(['products' => $products]);
?>
