<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <!-- Font Awesome CDN link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Fade-in animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-screen-lg">
        <a href="index.php" class="absolute top-0 left-0 p-4 text-xl text-gray-900">
            <i class="fas fa-home"></i> <!-- Font Awesome home icon -->
        </a>

        <!-- Message Card -->
        <div id="messageCard"
            class="hidden bg-white p-4 rounded shadow mb-4 text-center transition-colors duration-300"></div>

        <!-- Registration Form -->
        <form id="registrationForm" class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 space-y-4">
            <h2 class="text-center font-bold text-2xl mb-6 text-gray-800">Customer Registration</h2>

            <!-- Personal Information -->
            <div class="text-gray-700 font-semibold mb-2">Personal Information</div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="first_name">First Name <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="first_name" type="text" placeholder="First Name">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="middle_name">Middle Name</label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="middle_name" type="text" placeholder="Middle Name (Optional)">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="last_name">Last Name <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="last_name" type="text" placeholder="Last Name">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="suffix">Suffix</label>
                    <select
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="suffix">
                        <option value="">None</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>
                <div class="md:col-span-2 ">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="email">Email <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="email" type="email" placeholder="Email Address">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="phone">Phone Number <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="phone" type="text" placeholder="Phone Number">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="username">Username <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="username" type="text" placeholder="Choose a username">
                </div>

                <div class="md:col-span-2 ">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="password">Password <span
                            class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="password" type="password" placeholder="********">
                    <p class="text-xs text-gray-500">Password must be at least 6 characters.</p>
                </div>

                <div class="md:col-span-2 ">
                    <label class="block text-gray-700 text-sm font-medium mb-2" for="confirm_password">Confirm Password
                        <span class="text-red-500">*</span></label>
                    <input
                        class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                        id="confirm_password" type="password" placeholder="********">
                </div>
            </div>



            <div class="flex items-center justify-between mt-6">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition duration-150"
                    type="button" onclick="register()">
                    Register
                </button>
                <!-- Log In Button -->
                <div class="text-sm">
                    Already have an account?
                    <button class="text-blue-600 hover:text-blue-800 font-medium focus:outline-none" type="button"
                        onclick="redirectToLogin()">
                        Log In
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function redirectToLogin() {
            // Redirect to the login page
            window.location.href = "loginuser.php";
        }

        function validateForm() {
            const first_name = document.getElementById("first_name").value;
            const last_name = document.getElementById("last_name").value;
            const email = document.getElementById("email").value;
            const phone = document.getElementById("phone").value;
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;
            const confirm_password = document.getElementById("confirm_password").value;
            const messageCard = document.getElementById("messageCard");

            // Reset previous error messages
            messageCard.classList.add("hidden");
            messageCard.classList.remove("bg-red-100", "text-red-800");
            messageCard.classList.add("bg-green-100", "text-green-800");

            // Check if all required fields are filled
            if (!first_name || !last_name || !email || !phone || !username || !password || !confirm_password) {
                messageCard.textContent = "Please fill in all required fields.";
                messageCard.classList.remove("hidden");
                messageCard.classList.add("bg-red-100", "text-red-800");
                return false; // Stop form submission
            }

            // Basic email validation
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                messageCard.textContent = "Please enter a valid email address.";
                messageCard.classList.remove("hidden");
                messageCard.classList.add("bg-red-100", "text-red-800");
                return false; // Stop form submission
            }

            // Check if passwords match
            if (password !== confirm_password) {
                messageCard.textContent = "Passwords do not match.";
                messageCard.classList.remove("hidden");
                messageCard.classList.add("bg-red-100", "text-red-800");
                return false; // Stop form submission
            }

            // Check if password length is valid
            if (password.length < 6) {
                messageCard.textContent = "Password must be at least 6 characters.";
                messageCard.classList.remove("hidden");
                messageCard.classList.add("bg-red-100", "text-red-800");
                return false; // Stop form submission
            }

            return true; // All checks passed, proceed with form submission
        }

        function register() {
            if (!validateForm()) return; // If validation fails, prevent form submission

            const first_name = document.getElementById("first_name").value;
            const middle_name = document.getElementById("middle_name").value;
            const last_name = document.getElementById("last_name").value;
            const suffix = document.getElementById("suffix").value;
            const email = document.getElementById("email").value;
            const phone = document.getElementById("phone").value;
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;
            const messageCard = document.getElementById("messageCard");

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "registration.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);  // Log the response text for debugging
                    try {
                        const response = JSON.parse(xhr.responseText);
                        messageCard.textContent = response.message;
                        messageCard.classList.remove("hidden");

                        // Add animation and background color based on response
                        messageCard.classList.add("fade-in");
                        if (response.success) {
                            messageCard.classList.add("bg-green-100", "text-green-800");
                            messageCard.classList.remove("bg-red-100", "text-red-800");

                            // Redirect to index.php on success
                            setTimeout(() => window.location.href = "index.php", 1500); // Delay for smooth transition
                        } else {
                            messageCard.classList.add("bg-red-100", "text-red-800");
                            messageCard.classList.remove("bg-green-100", "text-green-800");
                        }

                        // Scroll to the message card if it's a success
                        messageCard.scrollIntoView({ behavior: "smooth", block: "center" });

                        // Remove fade-in class after animation completes
                        setTimeout(() => messageCard.classList.remove("fade-in"), 500);
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        messageCard.textContent = "Error processing the request.";
                        messageCard.classList.remove("hidden");
                        messageCard.classList.add("bg-red-100", "text-red-800");

                        // Scroll to the message card if it's visible
                        messageCard.scrollIntoView({ behavior: "smooth", block: "center" });
                    }
                }
            };

            xhr.send(`first_name=${encodeURIComponent(first_name)}&middle_name=${encodeURIComponent(middle_name)}&last_name=${encodeURIComponent(last_name)}&suffix=${encodeURIComponent(suffix)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`);
        }
    </script>
</body>

</html>