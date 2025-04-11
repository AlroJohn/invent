
<?php
include('../conn.php');
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    html {
  font-size: 80%; /* 100% / 0.8 = 125% */
}
  </style>
<body class="bg-gray-100">
<nav class="bg-green-900 text-white shadow-md p-4 w-full fixed top-0 left-0 z-50">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <button onclick="toggleSidebar()" class="text-white focus:outline-none lg:hidden mr-4">
                <!-- Mobile menu button -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
            <h1 class="text-3xl font-bold">Dad's Pinangat</h1>
        </div>
        <div class="flex items-center">
            <button onclick="confirmLogout()" class="bg-red-500 px-3 py-1 rounded text-white">Logout</button>
        </div>
    </div>
</nav>
<br><br>
   <!-- Sidebar and Main Content Container -->
    
<div class="flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 min-h-screen absolute inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 transition duration-200 ease-in-out">
        <!-- Close Button -->
        <div class="flex justify-end px-4">
            <button onclick="toggleSidebar()" class="text-white focus:outline-none lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Logo Section -->
        <div class="flex items-center space-x-2 px-4">
            <i class="fas fa-user-shield text-2xl"></i>
            <span class="text-xl font-bold">Admin</span>
        </div>

        <nav class="mt-6 space-y-2 text-white">
    <!-- Dashboard -->
    <a href="index.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
        <i class="fas fa-home mr-3"></i> Dashboard
    </a>

    <!-- Users -->
    <a href="users.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
        <i class="fas fa-users mr-3"></i> Users
    </a>

    <!-- Products -->
    <a href="products.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
        <i class="fas fa-box mr-3"></i> Products
    </a>

    <!-- Orders -->
    <a href="orders.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
        <i class="fas fa-shopping-cart mr-3"></i> Orders
    </a>

   
    <!-- Settings Dropdown 
    <div>
        <button onclick="toggleDropdown('settingsDropdown')" class="flex items-center justify-between w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 focus:outline-none">
            <div class="flex items-center">
                <i class="fas fa-cog mr-3"></i> Settings
            </div>
            <svg class="w-4 h-4 transform transition-transform" id="settingsIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="settingsDropdown" class="ml-6 mt-1 space-y-2 hidden">
            <a href="#" class="flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700">
                <i class="fas fa-wrench mr-3"></i> General
            </a>
            <a href="#" class="flex items-center py-2 px-4 rounded transition duration-200 hover:bg-gray-700">
                <i class="fas fa-lock mr-3"></i> Security
            </a>
        </div>
    </div>


    <a href="#" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
        <i class="fas fa-chart-bar mr-3"></i> Reports
    </a>-->
</nav>



    </aside>

    

<script>
    
    // Toggle modal visibility
    function toggleModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.toggle('hidden');
    }

    // Toggle sidebar
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("-translate-x-full");
    }

    // Toggle dropdown
    function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        dropdown.classList.toggle("hidden");
    }

    // Confirm logout
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout.php";
        }
    }
    function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("-translate-x-full");
}
</script>