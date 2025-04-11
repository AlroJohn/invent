<?php
include '../conn.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$offset = ($page - 1) * $limit;

try {
    // Prepare the query to fetch the total number of products
    if ($search) {
        $totalQuery = "SELECT COUNT(*) AS total FROM products WHERE name LIKE ?";
        $stmt = $conn->prepare($totalQuery);
        $searchTerm = "%" . $search . "%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRow = $totalResult->fetch_assoc();
        $totalProducts = $totalRow['total'];
        $stmt->close();

        // Fetch products with limit, offset, and search filter
        $query = "SELECT id, name, price, stock, image_path, expiration_date 
                  FROM products WHERE name LIKE ? LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $searchTerm, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $totalQuery = "SELECT COUNT(*) AS total FROM products";
        $totalResult = $conn->query($totalQuery);
        $totalRow = $totalResult->fetch_assoc();
        $totalProducts = $totalRow['total'];

        // Fetch products with limit and offset
        $query = "SELECT id, name, price, stock, image_path, expiration_date 
                  FROM products LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    // Return the response as JSON
    $response = [
        'products' => $products,
        'total' => $totalProducts,
        'page' => $page,
        'limit' => $limit
    ];

    echo json_encode($response);
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Handle errors
    echo json_encode(['error' => $e->getMessage()]);
    $conn->close();
}
?>
