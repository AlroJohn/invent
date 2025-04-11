<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Fade-in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
<div class="w-full max-w-xs">
    <a href="index.php" class="absolute top-0 left-0 p-4 text-xl text-gray-900">
        <i class="fas fa-home"></i> <!-- Font Awesome home icon -->
    </a>

    <!-- Message Card -->
    <div id="messageCard" class="hidden bg-white p-4 rounded shadow mb-4 text-center transition-colors duration-300"></div>
    
    <!-- Login Form -->
    <form id="loginForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8">
        <h2 class="text-center font-bold text-xl mb-4">Login</h2>

        <!-- Phone or Email Input -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="contact">Phone Number or Email</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="contact" type="text" placeholder="Enter phone number or email" required>
            <p id="contactError" class="text-red-500 text-xs italic hidden">Invalid phone number or email format.</p>
        </div>

        <!-- Password Input -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                   id="password" type="password" placeholder="********" required>
            
            <!-- View Password Checkbox -->
            <div class="flex items-center">
                <input type="checkbox" id="viewPassword" class="mr-2">
                <label for="viewPassword" class="text-sm text-gray-600">View Password</label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="button" onclick="login()">
                Sign In
            </button>
        </div>

        <!-- Register Button -->
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Don't have an account?</p>
            <a href="register.php" class="text-blue-500 hover:text-blue-700 font-bold">Register here</a>
        </div>
    </form>
</div>

<!-- Validation Script -->
<script>
    document.getElementById("contact").addEventListener("input", function () {
        const contactInput = this.value.trim();
        const contactError = document.getElementById("contactError");
        
        // Regular expressions for validation
        const phoneRegex = /^09\d{9}$/; // Matches "09XXXXXXXXX" (11-digit Philippine phone number)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email validation

        if (phoneRegex.test(contactInput) || emailRegex.test(contactInput)) {
            contactError.classList.add("hidden");
        } else {
            contactError.classList.remove("hidden");
        }
    });
</script>

<!-- Login Script -->
<script>
    function login() {
        const contact = document.getElementById("contact").value;
        const password = document.getElementById("password").value;
        const messageCard = document.getElementById("messageCard");

        // Check if contact input is valid
        const phoneRegex = /^09\d{9}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!phoneRegex.test(contact) && !emailRegex.test(contact)) {
            messageCard.textContent = "Invalid phone number or email format.";
            messageCard.classList.remove("hidden", "bg-green-100", "text-green-800");
            messageCard.classList.add("fade-in", "bg-red-100", "text-red-800");
            return;
        }

        // Send login request
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                messageCard.textContent = response.message;
                messageCard.classList.remove("hidden");

                // Add animation and background color based on response
                messageCard.classList.add("fade-in");
                if (response.success) {
                    messageCard.classList.add("bg-green-100", "text-green-800");
                    messageCard.classList.remove("bg-red-100", "text-red-800");
                    
                    // Redirect to the appropriate page based on the role
                    setTimeout(() => window.location.href = response.redirect, 500);
                } else {
                    messageCard.classList.add("bg-red-100", "text-red-800");
                    messageCard.classList.remove("bg-green-100", "text-green-800");
                }

                // Remove fade-in class after animation completes
                setTimeout(() => messageCard.classList.remove("fade-in"), 500);
            }
        };

        xhr.send(`contact=${encodeURIComponent(contact)}&password=${encodeURIComponent(password)}`);
    }

    // Toggle password visibility
    document.getElementById('viewPassword').addEventListener('change', function() {
        const passwordField = document.getElementById('password');
        passwordField.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>
