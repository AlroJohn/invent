<?php
include '../conn.php';

// Prevent PHP errors from being displayed in the output
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set JSON response header

// Debugging: Check if data is received
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(["success" => false, "message" => "Invalid request. No data received."]);
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Debugging: Check received values
if (empty($order_id) || empty($status)) {
    echo json_encode(["success" => false, "message" => "Missing required fields: Order ID or Status is empty."]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update order status
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    // Fix: `order_id` is VARCHAR, so bind as "ss" (string, string)
    $stmt->bind_param("ss", $status, $order_id);

    if (!$stmt->execute()) {
        throw new Exception("Error updating order: " . $stmt->error);
    }

    $stmt->close();

    // Get user ID and order details for SMS
    $order_query = "SELECT o.user_id, o.total_price, u.phone, u.username 
                   FROM orders o 
                   JOIN user u ON o.user_id = u.id 
                   WHERE o.order_id = ?";
    $order_stmt = $conn->prepare($order_query);

    if (!$order_stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    $order_stmt->bind_param("s", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    // Get order items for SMS
    $items_query = "SELECT oi.quantity, p.name, oi.price
                   FROM order_items oi
                   JOIN products p ON oi.product_id = p.id
                   WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("s", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    $items = [];

    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }

    $items_stmt->close();

    // If we have order data and user phone, send SMS
    $sms_sent = false;

    if ($order_result && $order_result->num_rows > 0) {
        $order_data = $order_result->fetch_assoc();
        $user_phone = $order_data['phone'];

        if (!empty($user_phone) && file_exists('../telerivet-php-client/telerivet.php')) {
            try {
                require_once '../telerivet-php-client/telerivet.php';

                // Telerivet credentials
                $api_key = "gPsXb_hjRro77qQgkVsdLptfExLruznHwYPe";
                $project_id = "PJ6a118a2fe2a6602e";

                // Initialize the Telerivet API
                $telerivet = new Telerivet_API($api_key);

                // Get a reference to the project
                $project = $telerivet->initProjectById($project_id);

                // Format message based on status
                $message = "Dear " . $order_data['username'] . ",\n\n";
                $message .= "Your order #" . $order_id . " has been " . strtoupper($status) . ".\n\n";

                // Include order details
                if (count($items) > 0) {
                    $message .= "Order Summary:\n";
                    foreach ($items as $item) {
                        $message .= "• " . $item['name'] . " x" . $item['quantity'] . " (₱" . number_format($item['price'], 2) . ")\n";
                    }
                    $message .= "\n";
                }

                $message .= "Total Amount: ₱" . number_format($order_data['total_price'], 2) . "\n\n";

                // Add specific message based on status
                if ($status == 'Processing') {
                    $message .= "We are now preparing your order. You'll receive another update when it's ready for shipping.";
                } elseif ($status == 'Shipped') {
                    $message .= "Your order has been shipped and is on its way to you!";
                } elseif ($status == 'Completed') {
                    $message .= "Your order has been completed. Thank you for your purchase!";
                } elseif ($status == 'Cancelled') {
                    $message .= "Your order has been cancelled. If you have any questions, please contact our customer service.";
                }

                // Send the message to customer
                $project->sendMessage(array(
                    'to_number' => $user_phone,
                    'content' => $message
                ));

                $sms_sent = true;
            } catch (Exception $e) {
                // Just log error, don't halt transaction
                error_log("SMS error: " . $e->getMessage());
            }
        }
    }

    $order_stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(["success" => true, "message" => "Order status updated successfully.", "sms_sent" => $sms_sent]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>