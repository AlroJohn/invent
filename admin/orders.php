<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: loginuser.php");
    exit();
}

// Check if user has appropriate role
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'employee') {
    header("Location: /invent/user/index.php");
    exit();
}

// If we get here, user is authenticated and authorized
include("header.php");
?>

<body class="bg-gray-100">

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Order Management</h2>

        <div class="bg-white shadow-md rounded-lg p-4">
            <table class="w-full border-collapse border border-gray-300 text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border p-2">Order ID</th>
                        <th class="border p-2">Customer Name</th>
                        <th class="border p-2">Customer Number</th>
                        <th class="border p-2">Date</th>
                        <th class="border p-2">Total</th>
                        <th class="border p-2">Status</th>
                        <th class="border p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Updated SQL query to join with customers table instead of store_owners
                    $sql = "SELECT 
                        orders.order_id, 
                        orders.total_price, 
                        orders.status, 
                        orders.created_at, 
                        user.username,
                        CONCAT(customers.first_name, ' ', customers.last_name) AS customer_name,
                        customers.phone AS customer_phone
                        FROM orders
                        JOIN user ON orders.user_id = user.id
                        JOIN customers ON user.email = customers.email
                        ORDER BY orders.created_at DESC";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($order = $result->fetch_assoc()) {
                            echo '<tr class="bg-white">';
                            echo '<td class="border p-2">' . htmlspecialchars($order['order_id']) . '</td>';
                            echo '<td class="border p-2">' . htmlspecialchars($order['customer_name']) . '</td>';
                            echo '<td class="border p-2">' . htmlspecialchars($order['customer_phone']) . '</td>';
                            echo '<td class="border p-2">' . date('F j, Y g:i A', strtotime($order['created_at'])) . '</td>';
                            echo '<td class="border p-2">₱' . number_format($order['total_price'], 2) . '</td>';

                            // Define status colors
                            $statusColors = [
                                "Pending" => "bg-yellow-500 text-white",
                                "Processing" => "bg-blue-500 text-white",
                                "Shipped" => "bg-purple-500 text-white",
                                "Completed" => "bg-green-500 text-white"
                            ];

                            // Get the current order status
                            $currentStatus = $order['status'];
                            $currentColor = isset($statusColors[$currentStatus]) ? $statusColors[$currentStatus] : "bg-gray-500 text-white";

                            // Status dropdown
                            echo '<td class="border p-2">';
                            echo '<select class="status-dropdown p-1 border rounded ' . $currentColor . '" data-order-id="' . $order['order_id'] . '">
                                <option value="Pending" class="bg-yellow-500 text-white"' . ($currentStatus == 'Pending' ? ' selected' : '') . '>Pending</option>
                                <option value="Processing" class="bg-blue-500 text-white"' . ($currentStatus == 'Processing' ? ' selected' : '') . '>Processing</option>
                                <option value="Shipped" class="bg-purple-500 text-white"' . ($currentStatus == 'Shipped' ? ' selected' : '') . '>Shipped</option>
                                <option value="Completed" class="bg-green-500 text-white"' . ($currentStatus == 'Completed' ? ' selected' : '') . '>Completed</option>
                                </select>';
                            echo '</td>';

                            // Add action buttons
                            echo '<td class="border p-2">';
                            echo '<button class="view-details bg-blue-500 text-white px-2 py-1 rounded text-sm mr-1" data-order-id="' . $order['order_id'] . '">Details</button>';
                            echo '<button class="print-qr bg-green-500 text-white px-2 py-1 rounded text-sm" data-order-id="' . $order['order_id'] . '">QR Code</button>';
                            echo '</td>';

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" class="border p-2 text-center">No orders found.</td></tr>';
                    }

                    $conn->close();
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <h3 class="text-xl font-bold mb-4">Order Details</h3>
            <div id="orderDetails"></div>
            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded close-modal">Close</button>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96 text-center">
            <h3 class="text-xl font-bold mb-4">Order QR Code</h3>
            <div id="qrCodeContainer" class="flex justify-center"></div>
            <button class="mt-4 bg-green-500 text-white px-4 py-2 rounded print-qr-code">Print</button>
            <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded close-qr-modal">Close</button>
        </div>
    </div>

    <!-- Include qrcode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        $(document).on('change', '.status-dropdown', function () {
            var orderId = $(this).data('order-id'); // Get order ID
            var newStatus = $(this).val(); // Get new status

            console.log("Updating order:", orderId, "New status:", newStatus); // Debugging

            $.ajax({
                url: 'update_order_status.php',
                type: 'POST',
                data: { order_id: orderId, status: newStatus },
                dataType: 'json',
                success: function (response) {
                    console.log(response); // Debug response
                    if (response.success) {
                        alert('Order status updated successfully!');
                        location.reload(); // Refresh page after successful update
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert('Error updating order status.');
                }
            });
        });


        $(document).ready(function () {
            // View order details
            $(document).on('click', '.view-details', function () {
                var orderId = $(this).data('order-id');

                $.ajax({
                    url: 'fetch_order_details.php',
                    type: 'POST',
                    data: { order_id: orderId },
                    success: function (response) {
                        $('#orderDetails').html(response);
                        $('#orderModal').removeClass('hidden');
                    }
                });
            });

            // Close modal
            $('.close-modal').on('click', function () {
                $('#orderModal').addClass('hidden');
            });

            // Print QR Code
            $(document).on('click', '.print-qr', function () {
                var orderId = $(this).data('order-id');
                var orderUrl = "order_details.php?order_id=" + orderId; // Replace with actual domain/path if needed

                // Clear previous QR codes
                $('#qrCodeContainer').empty();

                // Generate QR Code containing the Order Details URL
                new QRCode(document.getElementById("qrCodeContainer"), {
                    text: orderUrl,
                    width: 256,  // Larger size for readability
                    height: 256
                });

                // Show QR Modal
                $('#qrModal').removeClass('hidden');
            });

            // Close QR Modal
            $('.close-qr-modal').on('click', function () {
                $('#qrModal').addClass('hidden');
            });

            // Print QR Code
            $(document).on('click', '.print-qr-code', function () {
                var printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print QR Code</title></head><body>');
                printWindow.document.write('<div style="text-align:center;">');
                printWindow.document.write('<h2>Order QR Code</h2>');
                printWindow.document.write($('#qrCodeContainer').html()); // Print only QR code
                printWindow.document.write('</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
            });
        });
    </script>
</body>

</html>