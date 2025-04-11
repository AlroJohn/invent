<?php
// Prevent PHP errors from being displayed in the output
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start session and set content type to JSON
session_start();
header('Content-Type: application/json');

// Try to include database connection
try {
    include '../conn.php';
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Database connection error"]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Get and validate JSON input
$input = file_get_contents('php://input');
if (empty($input)) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit;
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

if (!isset($data['cart']) || empty($data['cart'])) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

$cart = $data['cart'];

// Begin transaction
try {
    $conn->begin_transaction();

    // Generate a unique order ID
    $order_id = uniqid();

    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Get customer information
    $user_query = "SELECT username, phone FROM user WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_info = $user_result->fetch_assoc();
    $user_stmt->close();

    // Insert the order record
    $sql = "INSERT INTO orders (order_id, user_id, total_price, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $order_id, $user_id, $total_price);
    $stmt->execute();
    $stmt->close();

    // Arrays to store ordered products and their updated stock
    $ordered_products = [];
    $updated_stock = [];

    // Insert each product into order_items
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    foreach ($cart as $item) {
        // Get product details before updating
        $product_query = "SELECT name, stock FROM products WHERE id = ?";
        $product_stmt = $conn->prepare($product_query);
        $product_stmt->bind_param("i", $item['id']);
        $product_stmt->execute();
        $product_result = $product_stmt->get_result();
        $product = $product_result->fetch_assoc();
        $product_stmt->close();

        // Save original product data
        $ordered_products[] = [
            'name' => $product['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'original_stock' => $product['stock']
        ];

        // Insert order item
        $stmt->bind_param("siid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();

        // Reduce stock quantity in the products table
        $new_stock = $product['stock'] - $item['quantity'];
        $update_stock_sql = "UPDATE products SET stock = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_stock_sql);
        $update_stmt->bind_param("ii", $new_stock, $item['id']);
        $update_stmt->execute();
        $update_stmt->close();

        // Save updated stock info
        $updated_stock[] = [
            'name' => $product['name'],
            'original_stock' => $product['stock'],
            'quantity_ordered' => $item['quantity'],
            'remaining_stock' => $new_stock
        ];
    }

    $stmt->close();

    // Try to send SMS if Telerivet is available
    $sms_sent = false;

    if (file_exists('../telerivet-php-client/telerivet.php')) {
        try {
            require_once '../telerivet-php-client/telerivet.php';

            // Telerivet credentials
            $api_key = "gPsXb_hjRro77qQgkVsdLptfExLruznHwYPe";
            $project_id = "PJ6a118a2fe2a6602e";

            // Initialize the Telerivet API
            $telerivet = new Telerivet_API($api_key);

            // Get a reference to the project
            $project = $telerivet->initProjectById($project_id);

            // Get owner phone from database
            $owner_query = "SELECT phone FROM user WHERE role = 'admin'";
            $owner_result = $conn->query($owner_query);

            if ($owner_result && $owner_result->num_rows > 0) {
                $owner = $owner_result->fetch_assoc();
                $store_owner_phone = $owner['phone'];

                // Format detailed message with order information
                $message = "New Order #" . $order_id . "!\n";

                // Add customer info if available
                if ($user_info) {
                    $message .= "Customer: " . $user_info['username'];
                    if (!empty($user_info['phone'])) {
                        $message .= " (" . $user_info['phone'] . ")";
                    }
                    $message .= "\n";
                }

                $message .= "\nORDERED PRODUCTS:\n";

                foreach ($ordered_products as $product) {
                    $subtotal = $product['quantity'] * $product['price'];
                    $message .= "• " . $product['name'] . " x" . $product['quantity'] . " = ₱" . number_format($subtotal, 2) . "\n";
                }

                $message .= "\nTotal Amount: ₱" . number_format($total_price, 2) . "\n";
                $message .= "Status: Pending\n";

                $message .= "\nUPDATED INVENTORY:\n";

                foreach ($updated_stock as $stock) {
                    $message .= "• " . $stock['name'] . ": " . $stock['remaining_stock'] . " left (was " . $stock['original_stock'] . ")\n";
                }

                // Send the message
                $project->sendMessage(array(
                    'to_number' => $store_owner_phone,
                    'content' => $message
                ));

                $sms_sent = true;
            }
        } catch (Exception $e) {
            // Just log error, don't halt transaction
            error_log("SMS error: " . $e->getMessage());
        }
    }

    $conn->commit(); // Commit the transaction
    echo json_encode(["success" => true, "order_id" => $order_id, "sms_sent" => $sms_sent]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
    exit;
}
?>