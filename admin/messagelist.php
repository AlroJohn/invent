<?php include 'header.php'; ?>
<br>
<div class="container mx-auto p-6"><br>
<div class="bg-white shadow-lg rounded-lg p-6 max-w-lg mx-auto">
    <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Users List</h2>
    <ul id="usersList" class="space-y-3"></ul>
</div>

<script>
    function fetchUsers() {
        fetch("fetch_users.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const usersList = document.getElementById("usersList");
                    usersList.innerHTML = "";
                    data.users.forEach(user => {
                        usersList.innerHTML += `
                            <li class="flex items-center p-3 rounded-lg shadow-md border transition-all cursor-pointer hover:bg-gray-100"
                                onclick="redirectToChat(${user.id})">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-bold">
                                    ${user.username.charAt(0).toUpperCase()}
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-800">${user.username}</span>
                                        <span class="text-sm px-2 py-1 rounded-full ${user.status === 'Online' ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-600'}">
                                            ${user.status}
                                        </span>
                                    </div>
                                    ${user.status === 'Offline' ? `<span class="text-xs text-gray-500">Last seen: ${user.last_seen}</span>` : ''}
                                </div>
                            </li>`;
                    });
                }
            });
    }

    function redirectToChat(userId) {
        window.location.href = `message.php?user_id=${userId}`;
    }

    // Fetch users every 5 seconds
    setInterval(fetchUsers, 5000);
    fetchUsers();
</script>
