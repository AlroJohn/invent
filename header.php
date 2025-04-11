<?php include 'conn.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <title>Landing Page</title>
</head>

<body>
  <style>
    html {
  font-size: 80%; /* 100% / 0.8 = 125% */
}
  </style>
  <!-- Navbar -->
  <!-- Navbar -->
<nav class="bg-gradient-to-r from-green-800 to-green-500 px-6 py-4 shadow-lg">
  <div class="container mx-auto flex items-center justify-between">
    <!-- Logo -->
    <a href="index.php" class="text-white text-2xl font-bold tracking-wide">DAD'S PINANGAT</a>

    <!-- Mobile Menu Button -->
    <button id="menu-btn" class="lg:hidden text-white focus:outline-none">
      <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>
    </button>

    <!-- Desktop Menu -->
    <div id="menu" class="hidden lg:flex items-center space-x-6">
      <a href="#products" class="text-white hover:text-gray-200 transition duration-300">Products</a>
      <a href="register.php" class="bg-white text-green-700 px-4 py-2 rounded-lg font-semibold shadow-md hover:bg-green-100 transition duration-300">Sign Up</a>
      <a href="loginuser.php" class="bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold shadow-md hover:bg-yellow-300 transition duration-300">Log In</a>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden lg:hidden flex flex-col items-center mt-4 space-y-2 bg-green-700 py-3 rounded-lg shadow-lg">
    <a href="#products" class="text-white block px-4 py-2 hover:bg-green-600 rounded transition duration-300">Products</a>
    <a href="register.php" class="text-white block px-4 py-2 hover:bg-green-600 rounded transition duration-300">Sign Up</a>
    <a href="loginuser.php" class="text-white block px-4 py-2 hover:bg-green-600 rounded transition duration-300">Log In</a>
  </div>
</nav>

<!-- JavaScript for Menu Toggle -->
<script>
  const menuBtn = document.getElementById("menu-btn");
  const menu = document.getElementById("mobile-menu");
  const menuIcon = document.getElementById("menu-icon");

  menuBtn.addEventListener("click", () => {
    menu.classList.toggle("hidden");
    menuIcon.classList.toggle("rotate-180");
  });
</script>
