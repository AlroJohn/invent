<?php
include '../conn.php';

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id']; // Keep as string since it's varchar(50)

    // Fetch order items for the specific order_id
    $sql = "SELECT oi.quantity, oi.price, p.name 
            FROM order_items AS oi 
            INNER JOIN products AS p ON oi.product_id = p.id 
            WHERE oi.order_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id); // "s" because order_id is a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table class="w-full border-collapse border border-gray-200">';
        echo '<thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Product</th>
                    <th class="border p-2">Qty</th>
                    <th class="border p-2">Price</th>
                    <th class="border p-2">Total</th>
                </tr>
              </thead>';
        echo '<tbody>';
        
        while ($item = $result->fetch_assoc()) {
            $total = $item['price'] * $item['quantity'];
            echo '<tr>';
            echo '<td class="border p-2">' . htmlspecialchars($item['name']) . '</td>';
            echo '<td class="border p-2">' . htmlspecialchars($item['quantity']) . '</td>';
            echo '<td class="border p-2">₱' . number_format($item['price'], 2) . '</td>';
            echo '<td class="border p-2">₱' . number_format($total, 2) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No order items found for this order.</p>';
    }

    $stmt->close();
    $conn->close();
}
?>
