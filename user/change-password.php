<?php include 'header.php'; ?>
<body class="bg-gray-100">
    <div class="lg:pl-64 min-h-screen flex flex-col">
        <div class="container mx-auto p-6">
            <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">Change Password</h2>
                
                <!-- Message Box -->
                <div id="messageBox" class="hidden text-center p-3 mb-4 rounded"></div>
                
                <!-- Change Password Form -->
                <form id="changePasswordForm">
                    <!-- Current Password -->
                    <div class="mb-4 relative">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" id="current_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" required>
                        <i class="fas fa-eye absolute right-3 top-10 cursor-pointer text-gray-600" onclick="togglePassword('current_password', this)"></i>
                    </div>

                    <!-- New Password -->
                    <div class="mb-4 relative">
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" id="new_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" required>
                        <i class="fas fa-eye absolute right-3 top-10 cursor-pointer text-gray-600" onclick="togglePassword('new_password', this)"></i>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4 relative">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" id="confirm_password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" required>
                        <i class="fas fa-eye absolute right-3 top-10 cursor-pointer text-gray-600" onclick="togglePassword('confirm_password', this)"></i>
                    </div>

                    <button type="button" onclick="changePassword()" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function changePassword() {
    const currentPassword = document.getElementById("current_password").value;
    const newPassword = document.getElementById("new_password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const messageBox = document.getElementById("messageBox");

    // Regular Expression: At least 6 characters, includes both letters and numbers
    const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{6,}$/;

    if (!passwordRegex.test(newPassword)) {
        messageBox.textContent = "Password must be at least 6 characters long and contain both letters and numbers.";
        messageBox.classList.remove("hidden", "bg-green-100", "text-green-800");
        messageBox.classList.add("bg-red-100", "text-red-800");
        return;
    }

    if (newPassword !== confirmPassword) {
        messageBox.textContent = "New passwords do not match!";
        messageBox.classList.remove("hidden", "bg-green-100", "text-green-800");
        messageBox.classList.add("bg-red-100", "text-red-800");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "change_password.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            messageBox.textContent = response.message;
            messageBox.classList.remove("hidden");

            if (response.success) {
                messageBox.classList.add("bg-green-100", "text-green-800");
                messageBox.classList.remove("bg-red-100", "text-red-800");
                document.getElementById("changePasswordForm").reset();
            } else {
                messageBox.classList.add("bg-red-100", "text-red-800");
                messageBox.classList.remove("bg-green-100", "text-green-800");
            }
        }
    };

    xhr.send(`current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}`);
}

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>
