<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: loginuser.php");
    exit();
}

if ($_SESSION['role'] == 'admin') {
} else if ($_SESSION['role'] == 'employee') {
    header("Location: /invent/admin/index.php");
    exit();
} else {
    header("Location: /invent/user/index.php");
    exit();
}

// If we get here, user is authenticated and authorized (admin only)
include("header.php");
?>
<!-- Main Content -->
<div class="flex-1 p-8">


    <!-- Table Section -->
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <!-- Flex container to align title & button in one row -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold mb-4">Employee's Data</h2>
            <button onclick="toggleModal()"
                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                Add New User
            </button>
        </div>
        <table class="w-full border-collapse border border-gray-300 text-left">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Username</th>
                    <th class="border p-2">Email</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">Password</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user = "SELECT * FROM user WHERE role != 'admin' AND role = 'employee' ORDER BY id DESC";
                $result = $conn->query($user);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='bg-white' id='user-row-" . $row['id'] . "'>";
                        echo "<td class='border p-2 username'>" . $row['username'] . "</td>";
                        echo "<td class='border p-2 email'>" . $row['email'] . "</td>";
                        echo "<td class='border p-2 role'>" . $row['role'] . "</td>";
                        echo "<td class='border p-2 relative'>
                        <span id='masked-pwd-" . $row['id'] . "'>••••••••</span>
                        <span id='actual-pwd-" . $row['id'] . "' class='hidden'>" . htmlspecialchars($row['password']) . "</span>
                        <button onclick='promptPasswordReveal(" . $row['id'] . ")' class='ml-2 text-blue-500'>
                            <i class='fas fa-eye'></i>
                        </button>
                    </td>";
                        echo "<td class='border p-2'>
                        <a href='javascript:void(0)' onclick='openEditModal(" . $row['id'] . ")' class='text-blue-500 hover:underline'>Edit</a> | 
                        <a href='#' onclick='confirmDelete(" . $row['id'] . ")' class='text-red-500 hover:underline'>Delete</a>
                    </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='border p-2 text-center'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Admin Password Verification Modal -->
    <div id="passwordVerificationModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Admin Verification</h3>
            <p class="mb-4 text-gray-600">Please enter your admin password to view this employee's password.</p>
            <input type="hidden" id="targetPasswordId">
            <div class="mb-4">
                <label for="adminPassword" class="block text-sm font-medium text-gray-600">Admin Password</label>
                <input type="password" id="adminPassword" class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex justify-end">
                <button onclick="closePasswordModal()"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                <button onclick="verifyAdminPassword()"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg ml-2 hover:bg-blue-600">Verify</button>
            </div>
        </div>
    </div>


    <!-- Modal for Add User Form -->
    <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Add New User</h3>
            <form id="addUserForm" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                    <input type="text" id="username" name="username"
                        class="w-full p-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border border-gray-300 rounded-lg"
                        required>
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-600">Role</label>
                    <select id="role" name="role" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                    <div class="flex items-center">
                        <input type="password" id="password" name="password"
                            class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <button type="button" id="generatePassword"
                            class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Generate</button>
                    </div>
                </div>
                <!-- View Password Checkbox -->
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="viewPassword" class="mr-2" />
                    <label for="viewPassword" class="text-sm text-gray-600">View Password</label>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg ml-2 hover:bg-green-600">Add User</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Edit User</h3>
            <form id="editUserForm" method="POST">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="mb-4">
                    <label for="editUsername" class="block text-sm font-medium text-gray-600">Username</label>
                    <input type="text" id="editUsername" name="username"
                        class="w-full p-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label for="editEmail" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" id="editEmail" name="email" class="w-full p-2 border border-gray-300 rounded-lg"
                        required>
                </div>
                <div class="mb-4">
                    <label for="editRole" class="block text-sm font-medium text-gray-600">Role</label>
                    <select id="editRole" name="role" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <option value="admin">Admin</option>
                        <option value="customer">Customer</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>

                <!-- Old Password Field -->
                <div class="mb-4">
                    <label for="oldPassword" class="block text-sm font-medium text-gray-600">Current Password</label>
                    <input type="password" id="oldPassword" name="old_password"
                        class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Enter current password">
                    <p class="text-xs text-gray-500 mt-1">Required only if changing password</p>
                </div>

                <!-- New Password Field -->
                <div class="mb-4">
                    <label for="editPassword" class="block text-sm font-medium text-gray-600">New Password</label>
                    <input type="password" id="editPassword" name="password"
                        class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Enter new password">
                    <p class="text-xs text-gray-500 mt-1">Leave blank if you don't want to change the password</p>
                </div>

                <!-- View Password Checkbox -->
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="viewPasswordEdit" class="mr-2">
                    <label for="viewPasswordEdit" class="text-sm text-gray-600">View Passwords</label>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="toggleEditModal()"
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg ml-2 hover:bg-green-600">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Function to show the verification modal
    function promptPasswordReveal(userId) {
        document.getElementById('targetPasswordId').value = userId;
        document.getElementById('adminPassword').value = '';
        document.getElementById('passwordVerificationModal').classList.remove('hidden');
    }

    // Function to close the verification modal
    function closePasswordModal() {
        document.getElementById('passwordVerificationModal').classList.add('hidden');
    }

    // Function to verify admin password and reveal the target password
    function verifyAdminPassword() {
        const adminPassword = document.getElementById('adminPassword').value;
        const targetPasswordId = document.getElementById('targetPasswordId').value;

        // Make AJAX call to verify admin password
        fetch('verify_admin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password: adminPassword }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show the actual password
                    document.getElementById('masked-pwd-' + targetPasswordId).classList.add('hidden');
                    document.getElementById('actual-pwd-' + targetPasswordId).classList.remove('hidden');

                    // Add timeout to hide the password again after 10 seconds
                    setTimeout(() => {
                        document.getElementById('masked-pwd-' + targetPasswordId).classList.remove('hidden');
                        document.getElementById('actual-pwd-' + targetPasswordId).classList.add('hidden');
                    }, 10000);

                    // Close the modal
                    closePasswordModal();
                } else {
                    // Show error message
                    alert('Incorrect admin password. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during verification. Please try again.');
            });
    }
</script>


<script>
    // Toggle modal visibility
    function toggleModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.toggle('hidden');
    }

    // AJAX form submission
    $("#addUserForm").submit(function (event) {
        event.preventDefault();  // Prevent default form submission

        // Gather form data
        var formData = {
            username: $("#username").val(),
            email: $("#email").val(),
            role: $("#role").val(),
            password: $("#password").val()  // Include the password in the data
        };

        // Send AJAX request
        $.ajax({
            type: "POST",
            url: "add_user.php",  // The PHP file that handles the database insertion
            data: formData,
            success: function (response) {
                // Handle successful form submission
                alert(response);  // Show success message (or handle as needed)
                toggleModal();  // Close the modal
                location.reload();  // Optionally, reload the page to show the updated table
            },
            error: function (xhr, status, error) {
                // Handle errors
                alert("There was an error: " + error);
            }
        });
    });


    // Toggle modal visibility for Edit User
    function toggleEditModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.toggle('hidden');
    }

    // Open Edit Modal and populate form fields
    function openEditModal(userId) {
        // Get the user data from the table
        const row = document.getElementById(`user-row-${userId}`);
        const username = row.querySelector('.username').innerText;
        const email = row.querySelector('.email').innerText;
        const role = row.querySelector('.role').innerText;

        // Populate the edit form with the current user data
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editEmail').value = email;
        document.getElementById('editRole').value = role.toLowerCase();

        // Don't populate the password field with the table value
        document.getElementById('editPassword').value = '';  // Clear password field

        // Show the Edit User modal
        toggleEditModal();
    }


    // Edit User AJAX form submission
    $("#editUserForm").submit(function (event) {
        event.preventDefault();  // Prevent default form submission

        // Get the new password and old password values
        const newPassword = $("#editPassword").val();
        const oldPassword = $("#oldPassword").val();

        // If new password is provided, old password is required
        if (newPassword && !oldPassword) {
            alert("Please enter the current password to change the password");
            return;
        }

        // Gather form data
        var formData = {
            user_id: $("#editUserId").val(),
            username: $("#editUsername").val(),
            email: $("#editEmail").val(),
            role: $("#editRole").val(),
            password: newPassword,
            old_password: oldPassword
        };

        // Send AJAX request
        $.ajax({
            type: "POST",
            url: "edit_user.php",  // The PHP file that handles the database update
            data: formData,
            success: function (response) {
                // Handle successful form submission
                alert(response);  // Show success message (or handle as needed)
                toggleEditModal();  // Close the modal
                location.reload();  // Optionally, reload the page to show the updated table
            },
            error: function (xhr, status, error) {
                // Handle errors
                alert("There was an error: " + error);
            }
        });
    });

    // Update the password visibility toggle to include old password field
    document.getElementById('viewPasswordEdit').addEventListener('change', function () {
        const newPasswordField = document.getElementById('editPassword');
        const oldPasswordField = document.getElementById('oldPassword');

        // Toggle password visibility based on checkbox status
        if (this.checked) {
            newPasswordField.type = 'text';  // Show new password
            oldPasswordField.type = 'text';  // Show old password
        } else {
            newPasswordField.type = 'password';  // Hide new password
            oldPasswordField.type = 'password';  // Hide old password
        }
    });

    // Add event listener to the 'Generate' button
    document.getElementById('generatePassword').addEventListener('click', function () {
        const generatedPassword = generateRandomPassword();
        document.getElementById('password').value = generatedPassword;
    });

    // Toggle password visibility
    document.getElementById('viewPassword').addEventListener('change', function () {
        const passwordField = document.getElementById('password');
        // Toggle password visibility based on checkbox status
        if (this.checked) {
            passwordField.type = 'text';  // Show password
        } else {
            passwordField.type = 'password';  // Hide password
        }
    });

    // Function to generate a random password
    function generateRandomPassword() {
        const length = 8;  // Password length
        const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        return password;
    }

    // JavaScript function to confirm deletion
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = "delete_user.php?id=" + userId;
        }
    }

</script>



</body>

</html>