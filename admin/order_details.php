<?php
include '../conn.php';

if (!isset($_GET['order_id'])) {
    echo "Invalid order ID.";
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql = "SELECT orders.order_id, orders.total_price, orders.status, orders.created_at,
               user.username AS customer_name, store_owners.owner_phone
        FROM orders
        JOIN user ON orders.user_id = user.id
        JOIN store_owners ON user.email = store_owners.owner_email
        WHERE orders.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch order items
$sql_items = "SELECT oi.quantity, oi.price, p.name 
              FROM order_items AS oi 
              INNER JOIN products AS p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";

$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("s", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="container mx-auto bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Order Details</h2>

        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['owner_phone']); ?></p>
        <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
        <p><strong>Total:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

        <h3 class="text-lg font-semibold mt-4">Order Items</h3>
        <table class="w-full border-collapse border border-gray-200 mt-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Product</th>
                    <th class="border p-2">Qty</th>
                    <th class="border p-2">Price</th>
                    <th class="border p-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $result_items->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?php echo htmlspecialchars($item['name']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td class="border p-2">₱<?php echo number_format($item['price'], 2); ?></td>
                    <td class="border p-2">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="index.php" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Back</a>
    </div>

</body>
</html>
