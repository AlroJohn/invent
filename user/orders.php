<?php include 'header.php'; ?>
<body class="bg-gray-100">
<div class="lg:pl-64 min-h-screen flex flex-col">
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Order History</h2>

    <?php
    include '../conn.php';

    if (!isset($_SESSION['user_id'])) {
        echo '<p class="text-red-500">You must be logged in to view your order history.</p>';
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Fetch orders for the logged-in user
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($order = $result->fetch_assoc()) {
            $order_id = $order['order_id'];
            echo '<div class="bg-white shadow-md rounded-lg p-4 mb-6 printable" id="order-' . htmlspecialchars($order_id) . '">';
            echo '<h3 class="text-lg font-semibold mb-2">Order ID: ' . htmlspecialchars($order_id) . '</h3>';
            echo '<p class="text-gray-600 mb-2 print-hide">Status: <span class="font-semibold">' . htmlspecialchars($order['status']) . '</span></p>';
            echo '<p class="text-gray-500 mb-4">Date: ' . htmlspecialchars($order['created_at']) . '</p>';

            // Fetch order items
            $sql_items = "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
            $stmt_items = $conn->prepare($sql_items);
            $stmt_items->bind_param("s", $order_id);
            $stmt_items->execute();
            $items_result = $stmt_items->get_result();

            if ($items_result->num_rows > 0) {
                echo '<table class="w-full border-collapse border border-gray-200 text-left">';
                echo '<thead class="bg-gray-200">';
                echo '<tr><th class="border p-2">Product</th><th class="border p-2">Quantity</th><th class="border p-2">Price</th><th class="border p-2">Total</th></tr>';
                echo '</thead>';
                echo '<tbody>';
                
                $total_price = 0;
                while ($item = $items_result->fetch_assoc()) {
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    echo '<tr>';
                    echo '<td class="border p-2">' . htmlspecialchars($item['name']) . '</td>';
                    echo '<td class="border p-2">' . htmlspecialchars($item['quantity']) . '</td>';
                    echo '<td class="border p-2">₱' . number_format($item['price'], 2) . '</td>';
                    echo '<td class="border p-2">₱' . number_format($item_total, 2) . '</td>';
                    echo '</tr>';
                }

                echo '<tr class="bg-gray-100 font-semibold">';
                echo '<td colspan="3" class="border p-2">Total Amount</td>';
                echo '<td class="border p-2">₱' . number_format($total_price, 2) . '</td>';
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="text-gray-500">No items found for this order.</p>';
            }

           ;


            echo '</div>';
        }
    } else {
        echo '<p class="text-gray-500">No orders found.</p>';
    }

    $stmt->close();
    $conn->close();
    ?>
</div>
</div>

<!-- Print styling to hide button and status -->
<style>
@media print {
    .print-hide { display: none; } /* Hides button and status in print */
    .printable { padding: 20px; } /* Ensures proper layout */
}
</style>

<script>
function printOrder(orderId) {
    var orderDiv = document.getElementById(orderId);
    var printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>Print Order</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    printWindow.document.write('th { background-color: #f4f4f4; }');
    printWindow.document.write('.print-hide { display: none; }'); /* Hide button & status */
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(orderDiv.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

</body>
</html>
