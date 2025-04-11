<?php
session_start();
include('../conn.php'); // Include your database connection file
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE user SET last_activity = NOW() WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}

// Fetch customer data from the database
$user_id = $_SESSION['user_id'];

// Prepare SQL query to fetch the email from the user table
$query = "SELECT email, phone FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind the user_id to the prepared statement
$stmt->execute();
$stmt->store_result();

// Check if the user exists
if ($stmt->num_rows > 0) {
    // Bind the result to variables
    $stmt->bind_result($user_email, $user_phone);
    $stmt->fetch(); // Fetch the result
}

// Now fetch customer details using the email
$query = "SELECT * FROM customers WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email); // Bind email (string)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    $first_name = $customer['first_name'];
    $middle_name = $customer['middle_name'];
    $last_name = $customer['last_name'];
    $suffix = $customer['suffix'];
    $customer_email = $customer['email'];
    $customer_phone = $customer['phone'];

    // Format full name with middle name and suffix handling
    $full_name = $first_name;
    if (!empty($middle_name)) {
        $full_name .= " " . $middle_name;
    }
    $full_name .= " " . $last_name;
    if (!empty($suffix)) {
        $full_name .= " " . $suffix;
    }
} else {
    echo "Customer data not found.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Sidebar animation */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.close {
            transform: translateX(-100%);
        }

        /* Active link styling */
        .active {
            background: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-gray-100">
    <style>
        html {
            font-size: 85%;
            /* Adjusted font size */
        }
    </style>

    <!-- Sidebar -->
    <div id="sidebar"
        class="sidebar fixed inset-y-0 left-0 bg-blue-800 text-white w-64 p-4 transform -translate-x-full lg:translate-x-0 shadow-lg">

        <!-- Sidebar Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <i class="fas fa-user-circle text-2xl"></i>
                <span class="text-xl font-bold ml-2">Customer Portal</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <ul class="space-y-4">
            <li>
                <a href="index.php"
                    class="flex items-center px-4 py-2 rounded hover:bg-blue-700 transition-all duration-300 active">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="orders.php"
                    class="flex items-center px-4 py-2 rounded hover:bg-blue-700 transition-all duration-300">
                    <i class="fas fa-shopping-cart mr-3"></i> My Orders
                </a>
            </li>
            <li>
                <a href="product.php"
                    class="flex items-center px-4 py-2 rounded hover:bg-blue-700 transition-all duration-300">
                    <i class="fas fa-store mr-3"></i> Shop Products
                </a>
            </li>
            <li>
                <a href="profile.php"
                    class="flex items-center px-4 py-2 rounded hover:bg-blue-700 transition-all duration-300">
                    <i class="fas fa-user mr-3"></i> My Profile
                </a>
            </li>
            <li class="mt-auto">
                <a href="logout.php"
                    class="flex items-center px-4 py-2 rounded bg-red-600 hover:bg-red-700 transition-all duration-300"
                    onclick="return confirmLogout()">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="ml-0 lg:ml-64 p-4 transition-all duration-300">
        <!-- Top Navigation -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
            <div class="relative">
                <button onclick="toggleDropdown()" class="flex items-center space-x-2 focus:outline-none">
                    <span class="hidden md:inline text-gray-700"><?php echo htmlspecialchars($full_name); ?></span>
                    <i class="fas fa-user-circle text-blue-800 text-2xl"></i>
                </button>
                <div id="dropdownMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        onclick="return confirmLogout()">Logout</a>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Orders Status Card -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-800">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Orders</p>
                        <h3 class="font-bold text-lg">0</h3>
                    </div>
                </div>
            </div>

            <!-- Pending Orders Card -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Pending Orders</p>
                        <h3 class="font-bold text-lg">0</h3>
                    </div>
                </div>
            </div>

            <!-- Completed Orders Card -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Completed Orders</p>
                        <h3 class="font-bold text-lg">0</h3>
                    </div>
                </div>
            </div>

            <!-- Wishlist Items Card -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-800">
                        <i class="fas fa-heart text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Wishlist Items</p>
                        <h3 class="font-bold text-lg">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Customer Information</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-600 mb-2">Personal Details</h4>
                        <p><span class="text-gray-500">Name:</span> <?php echo htmlspecialchars($full_name); ?></p>
                        <p><span class="text-gray-500">Email:</span> <?php echo htmlspecialchars($customer_email); ?>
                        </p>
                        <p><span class="text-gray-500">Phone:</span> <?php echo htmlspecialchars($customer_phone); ?>
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600 mb-2">Account Activity</h4>
                        <p><span class="text-gray-500">Member Since:</span>
                            <?php echo date('F j, Y', strtotime($customer['created_at'])); ?></p>
                        <p><span class="text-gray-500">Last Login:</span> Today</p>
                        <a href="profile.php" class="mt-3 inline-block text-blue-600 hover:underline">Update Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Toggle Button -->
    <button onclick="toggleSidebar()"
        class="fixed top-4 left-4 lg:hidden bg-blue-700 text-white p-2 rounded shadow-lg z-20">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("-translate-x-full");
        }

        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function toggleDropdown() {
            document.getElementById("dropdownMenu").classList.toggle("hidden");
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            if (!e.target.closest('.relative')) {
                document.getElementById("dropdownMenu").classList.add("hidden");
            }
        });
    </script>
</body>

</html>