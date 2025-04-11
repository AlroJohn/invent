<?php 
include("header.php"); 

// Fetch dynamic data from database
$total_users_query = "SELECT COUNT(*) AS total_users FROM user";
$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_sales_query = "SELECT SUM(total_price) AS total_sales FROM orders";
$total_products_query = "SELECT COUNT(*) AS total_products FROM products"; // Assume you track visitors

$total_users = $conn->query($total_users_query)->fetch_assoc()['total_users'] ?? 0;
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['total_orders'] ?? 0;
$total_sales = $conn->query($total_sales_query)->fetch_assoc()['total_sales'] ?? 0;
$total_products = $conn->query($total_products_query)->fetch_assoc()['total_products'] ?? 0;

?>

<!-- Main Content -->
<div class="flex-1 p-8">
    <h2 class="text-2xl font-bold mb-6">Dashboard Overview</h2>

    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c.6 0 1 .4 1 1v3h3c.6 0 1 .4 1 1s-.4 1-1-1h-3v3c0 .6-.4 1-1 1s-1-.4-1-1v-3H8c-.6 0-1-.4-1-1s.4-1 1-1h3V9c0-.6.4-1 1-1z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-semibold text-gray-800"><?= $total_users; ?></div>
                    <div class="text-sm text-gray-500">Total Users</div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="text-lg font-semibold text-gray-800"><?= $total_orders; ?></div>
                    <div class="text-sm text-gray-500">Total Orders</div>
                </div>
            </div>
        </div>

        <!-- Total Products -->
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-12 w-12 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l1.45-2.9A2 2 0 015.25 6h13.5a2 2 0 011.8 1.1L21 10M3 10l9 6m0 0l9-6m-9 6v5m0-5L3 10m18 0l-9 6" />
            </svg>
        </div>
        <div class="ml-4">
            <div class="text-lg font-semibold text-gray-800"><?= $total_products; ?></div>
            <div class="text-sm text-gray-500">Total Products</div>
        </div>
    </div>
</div>


        <!-- Total Sales -->
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.866 0-7 1.79-7 4s3.134 4 7 4 7-1.79 7-4-3.134-4-7-4zm0 0V6m0 10v2m-3-2a3 3 0 106 0" />
            </svg>
        </div>
        <div class="ml-4">
            <div class="text-lg font-semibold text-gray-800">₱<?= number_format($total_sales, 2); ?></div>
            <div class="text-sm text-gray-500">Total Sales</div>
        </div>
    </div>
</div>

    </div>
</div>

<script>
    // Toggle sidebar on mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    }

    // Toggle dropdown for users and settings
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const icon = document.querySelector(`#${dropdownId} ~ svg`);
        
        dropdown.classList.toggle("hidden");
        icon.classList.toggle("rotate-180");
    }

    // Confirm logout
    function confirmLogout() {
        const isConfirmed = confirm("Are you sure you want to log out?");
        if (isConfirmed) {
            window.location.href = "logout.php"; // or handle logout as necessary
        }
    }
</script>

</body>
</html>
