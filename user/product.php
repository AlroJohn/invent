<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: loginuser.php");
    exit();
}

if ($_SESSION['role'] != 'customer') {
    header("Location: /invent/admin/index.php");
    exit();
}
include 'header.php'; ?>
<div class="lg:pl-64 min-h-screen flex flex-col bg-gray-100">


    <!-- Product List -->
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-4">Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // Fetch products from the database, including the image path
// Fetch products from the database, including the image path
            $sql = "SELECT id, name, price, stock, image_path, expiration_date 
        FROM products 
        WHERE stock > 0 AND expiration_date >= CURDATE()"; // Exclude expired products
            
            $result = $conn->query($sql);


            if ($result->num_rows > 0) {
                // Output each product
                while ($row = $result->fetch_assoc()) {
                    echo '
        <div class="product bg-white rounded-lg shadow-lg p-4 transform transition-transform duration-300 hover:scale-105" data-id="' . $row['id'] . '">
            <!-- Display the product image with hover effect -->
            <div class="relative group">
                <img src="' . (!empty($row['image_path']) ? $row['image_path'] : 'path/to/default/image.jpg') . '" 
                     alt="' . htmlspecialchars($row['name']) . '" 
                     class="w-full h-48 object-cover rounded-md group-hover:scale-110 transition-transform duration-300 ease-in-out cursor-pointer" 
                     onclick="openModal(this.src)">
                
                <!-- Overlay with opacity and text on hover -->
                <div class="absolute inset-0 bg-black opacity-50 hidden group-hover:block z-10"></div>
                <div class="absolute inset-0 flex justify-center items-center text-white font-semibold hidden group-hover:block z-20">View Full Image</div>
            </div>
            <h3 class="text-lg font-semibold mt-4">' . htmlspecialchars($row['name']) . '</h3>
            <p class="text-gray-700">₱' . number_format($row['price'], 2) . '</p>
            <p class="text-gray-500">Stock: ' . $row['stock'] . ' available</p>
            <p class="text-gray-500">Expiration Date: ' . date("F j, Y", strtotime($row['expiration_date'])) . '</p>
            <button class="add-to-cart flex items-center gap-2 bg-blue-500 text-white py-1 px-3 rounded text-sm hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
        data-stock="' . $row['stock'] . '"
        ' . ($row['stock'] == 0 ? 'disabled' : '') . '>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l1.6 9m0 0h9.4m-9.4 0L5 16a2 2 0 002 2h9a2 2 0 002-2l1.4-7M16 10h4M9 21h.01M15 21h.01" />
    </svg>
    ' . ($row['stock'] == 0 ? 'Out of Stock' : 'Add') . '
</button>

        </div>';
                }
            } else {
                echo '<p class="text-gray-700">No products available.</p>';
            }

            // Close the connection
            $conn->close();
            ?>

            <!-- Modal Structure -->
            <div id="imageModal"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50">
                <div class="relative">
                    <img id="modalImage" class="max-w-full max-h-screen rounded-lg shadow-lg">
                    <button
                        class="absolute top-2 right-2 bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center"
                        onclick="closeModal()">
                        &times;
                    </button>

                </div>
            </div>

        </div>


    </div>

    <!-- Cart Card -->
    <div id="cart-card" class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg w-80 p-4">
        <!-- Cart Header -->
        <h3 class="text-lg font-semibold mb-2 flex justify-between items-center">
            <span id="cart-title">Your Cart</span>
            <button id="minimize-cart" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <!-- Minimize Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </h3>
        <ul id="cart-items" class="space-y-2">
            <!-- Cart items will be dynamically inserted here -->
        </ul>
        <p id="cart-total" class="text-lg font-semibold mt-2 text-gray-700"></p>
        <div class="flex justify-between items-center mt-4">
            <button id="clear-cart" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Clear</button>
            <button id="checkout" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Checkout</button>
        </div>
    </div>

    <!-- Minimized Cart Button -->
    <div id="minimized-cart-btn"
        class="fixed bottom-4 right-4 bg-green-500 text-white py-3 px-6 rounded hover:bg-green-600 hidden">
        Cart
        <!-- Red Dot Notification -->
        <span id="cart-badge"
            class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center hidden">
            1
        </span>
    </div>
</div>

<script>
    // Initialize or load the cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Function to update the cart display
    function updateCartDisplay() {
        const cartItems = document.getElementById('cart-items');
        const cartTotal = document.getElementById('cart-total');
        const cartBadge = document.getElementById('cart-badge'); // Red dot badge
        const minimizedCartBtn = document.getElementById('minimized-cart-btn'); // Minimized cart button

        cartItems.innerHTML = ''; // Clear current list of items

        if (cart.length === 0) {
            cartTotal.textContent = 'Your cart is empty.';
            cartBadge.classList.add('hidden'); // Hide the red dot when cart is empty
            minimizedCartBtn.classList.add('hidden'); // Hide minimized cart button when cart is empty
            return;
        }

        // Add each cart item to the list
        cart.forEach(item => {
            const li = document.createElement('li');
            li.classList.add('flex', 'justify-between', 'items-center', 'p-2', 'border-b');

            li.innerHTML = `
            <span>${item.name} - ₱${item.price.toFixed(2)} x ${item.quantity}</span>
            <div class="flex items-center">
                <span>Qty: ${item.quantity}</span>
                <button class="ml-4 text-red-500 hover:text-red-700 remove-item" data-id="${item.id}">
                    Remove
                </button>
            </div>
        `;
            cartItems.appendChild(li);
        });

        // Display total price
        const totalPrice = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        cartTotal.textContent = `Total: ₱${totalPrice.toFixed(2)}`;

        // Show the red dot with the number of items
        if (cart.length > 0) {
            cartBadge.textContent = cart.length; // Set the number of items in the red dot
            cartBadge.classList.remove('hidden'); // Show the red dot
            minimizedCartBtn.classList.remove('hidden'); // Show the minimized cart button
        }
    }

    // Function to add product to cart
    function addToCart(product) {
        const existingProduct = cart.find(item => item.id === product.id);

        if (existingProduct) {
            // Prevent exceeding available stock
            if (existingProduct.quantity >= product.stock) {
                alert("You've reached the maximum stock available for this product.");
                return;
            }
            existingProduct.quantity += 1; // Increase quantity
        } else {
            cart.push({ ...product, quantity: 1 }); // Add new product
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }


    // Function to remove product from cart
    function removeFromCart(itemId) {
        cart = cart.filter(item => item.id !== itemId); // Remove the item from the cart by id
        localStorage.setItem('cart', JSON.stringify(cart)); // Save updated cart to localStorage
        updateCartDisplay(); // Update cart display
    }

    // Clear Cart functionality
    function clearCart() {
        cart = [];
        localStorage.setItem('cart', JSON.stringify(cart)); // Clear cart in localStorage
        updateCartDisplay(); // Refresh display
    }

    // Event listeners for "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productDiv = this.closest('.product');
            const productId = productDiv.getAttribute('data-id');
            const productName = productDiv.querySelector('h3').textContent;
            const productStock = parseInt(this.getAttribute('data-stock')); // Get stock value

            // Parse price correctly by removing non-numeric characters
            const productPrice = parseFloat(productDiv.querySelector('p').textContent.replace(/[^\d.-]/g, ''));

            if (isNaN(productPrice)) {
                console.error('Invalid price value');
                return;
            }

            if (productStock <= 0) {
                alert("This product is out of stock!");
                return;
            }

            const product = { id: productId, name: productName, price: productPrice, stock: productStock };
            addToCart(product);
        });
    });



    // Event listener for "Remove" button
    document.getElementById('cart-items').addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item')) {
            const itemId = event.target.getAttribute('data-id'); // Get the id of the item to remove
            removeFromCart(itemId); // Remove the item from the cart
        }
    });

    // Clear Cart button functionality
    document.getElementById('clear-cart').addEventListener('click', function () {
        clearCart(); // Clear the cart
    });

    // Initial cart update on page load
    updateCartDisplay();


</script>
<script>
    // Minimize cart functionality
    const minimizeButton = document.getElementById('minimize-cart');
    const cartCard = document.getElementById('cart-card');
    const cartTitle = document.getElementById('cart-title');
    const minimizedCartBtn = document.getElementById('minimized-cart-btn');

    // Toggle visibility of cart content
    minimizeButton.addEventListener('click', function () {
        const cartContent = cartCard.querySelector('ul, p, .flex');
        cartContent.classList.toggle('hidden');

        // Hide the entire cart card when minimized
        cartCard.classList.toggle('hidden');

        // Toggle the minimize icon (change to an expand icon)
        const iconPath = minimizeButton.querySelector('path');
        iconPath.setAttribute('d', iconPath.getAttribute('d') === 'M19 9l-7 7-7-7' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7');

        // Show or hide the minimized "Cart" button
        minimizedCartBtn.classList.toggle('hidden');
        cartTitle.classList.toggle('hidden');
    });

    // Open cart when the "Cart" button is clicked
    minimizedCartBtn.addEventListener('click', function () {
        cartCard.classList.remove('hidden'); // Show the entire cart card
        cartCard.querySelector('ul, p, .flex').classList.remove('hidden'); // Show cart items
        minimizedCartBtn.classList.add('hidden'); // Hide minimized button
        cartTitle.classList.remove('hidden'); // Show the title and minimize button
    });
</script>
<script>
    function openModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
    }

    document.getElementById('checkout').addEventListener('click', function () {
        if (cart.length === 0) {
            alert("Your cart is empty!");
            return;
        }

        // Check if any item exceeds stock
        for (let item of cart) {
            const stock = document.querySelector(`.product[data-id="${item.id}"]`).querySelector('.add-to-cart').getAttribute('data-stock');
            if (item.quantity > parseInt(stock)) {
                alert(`The quantity of "${item.name}" exceeds available stock!`);
                return;
            }
        }

        // Ask for confirmation before checkout
        if (!confirm("Are you sure you want to proceed with checkout?")) {
            return;
        }

        // Show loading state
        const checkoutBtn = document.getElementById('checkout');
        const originalText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = 'Processing...';
        checkoutBtn.disabled = true;

        // Proceed with checkout
        fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ cart: cart })
        })
            .then(response => response.json())
            .then(data => {
                // Restore button state
                checkoutBtn.innerHTML = originalText;
                checkoutBtn.disabled = false;

                if (data.success) {
                    // Create a success modal instead of simple alert
                    const successModal = document.createElement('div');
                    successModal.className = 'fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50';
                    successModal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-xl font-bold text-green-600 mb-4">Order Placed Successfully!</h3>
                    <p class="mb-2">Your order #${data.order_id} has been placed and is being processed.</p>
                    <p class="mb-4">An SMS notification has been sent to the store owner with your order details and updated inventory information.</p>
                    <div class="flex justify-end">
                        <button id="closeSuccessModal" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                            Continue Shopping
                        </button>
                    </div>
                </div>
            `;
                    document.body.appendChild(successModal);

                    // Handle closing the modal
                    document.getElementById('closeSuccessModal').addEventListener('click', function () {
                        document.body.removeChild(successModal);
                        // Clear cart
                        localStorage.removeItem('cart');
                        cart = [];
                        updateCartDisplay();
                        // Redirect to orders page
                        window.location.href = 'orders.php';
                    });
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                checkoutBtn.innerHTML = originalText;
                checkoutBtn.disabled = false;
                alert("An error occurred during checkout. Please try again.");
            });
    });



</script>