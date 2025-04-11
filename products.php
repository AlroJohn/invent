<?php include 'header.php'; ?>
  <style>#qrCode {
    border: 2px solid white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
    <!-- Product List -->
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-semibold mb-4">Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
// Fetch products from the database, including the image path
$sql = "SELECT id, name, price, stock, image_path, expiration_date FROM products WHERE stock > 0";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output each product
    while ($row = $result->fetch_assoc()) {
        // Clean up the image path and remove '../' if present
        $image = !empty($row['image_path']) ? str_replace('../', '', $row['image_path']) : 'https://via.placeholder.com/150';

        echo '
        <div class="product bg-white rounded-lg shadow-lg p-4 transform transition-transform duration-300 hover:scale-105" data-id="' . $row['id'] . '">
            <div class="relative group">
                <img src="' . $image . '" 
     alt="' . htmlspecialchars($row['name']) . '" 
     class="w-full h-48 object-cover rounded-md group-hover:scale-110 transition-transform duration-300 ease-in-out cursor-pointer" 
     onclick="openModal(\'' . htmlspecialchars(json_encode([
         'name' => $row['name'],
         'stock' => $row['stock'],
         'expiration_date' => $row['expiration_date'] ?? 'N/A',
         'image' => $image // Include image path here
     ]), ENT_QUOTES) . '\')">

                <div class="absolute inset-0 bg-black opacity-50 hidden group-hover:block z-10"></div>
                <div class="absolute inset-0 flex justify-center items-center text-white font-semibold hidden group-hover:block z-20">View Full Image</div>
            </div>
            <h3 class="text-lg font-semibold mt-4">' . htmlspecialchars($row['name']) . '</h3>
            <p class="text-gray-700">₱' . number_format($row['price'], 2) . '</p>
            <p class="text-gray-500">Stock: ' . $row['stock'] . ' available</p>
        </div>';
        
    }
} else {
    echo '<p class="text-gray-700">No products available.</p>';
}

// Close the connection
$conn->close();
?>


<!-- Modal Structure -->
<div id="imageModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50">
    <div class="relative bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
        <button class="absolute top-2 right-2 bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center"
            onclick="closeModal()">
            &times;
        </button>
        <img id="modalImage" class="max-w-full max-h-72 mb-4 rounded-lg shadow-lg" alt="Product Image">
    </div>
</div>



</div>


    </div>

<script>

function openModal(productData) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    // Parse the product data from JSON
    const product = JSON.parse(productData);

    // Set the image source in the modal
    modalImage.src = product.image || 'https://via.placeholder.com/150';

    // Show the modal
    modal.classList.remove('hidden');
}





function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
}

</script>
