<?php include 'header.php'; ?>
<div class="container mx-auto p-6">
    <div class="max-w-lg mx-auto bg-white p-4 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">Message Admin</h2>
        <div id="chatBox" class="h-80 overflow-y-auto border p-3 rounded mb-3"></div>
        <div class="flex">
            <input type="text" id="messageInput" class="w-full border rounded p-2" placeholder="Type a message...">
            <button onclick="sendMessage()" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Send</button>
        </div>
    </div>
</div>

<script>
    const chatWith = 1; // Change this dynamically based on the admin/user
    function fetchMessages() {
    fetch(`fetch_messages.php?chat_with=${chatWith}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chatBox = document.getElementById("chatBox");
                chatBox.innerHTML = "";
                data.messages.forEach(msg => {
                    const isSender = msg.sender_id != chatWith; // Check if the message is from the logged-in user
                    chatBox.innerHTML += `<div class="p-2 mb-2 ${isSender ? 'text-right' : 'text-left'}">
                        <span class="inline-block px-3 py-1 rounded ${isSender ? 'bg-green-500 text-white' : 'bg-gray-200 text-black'}">
                            ${msg.message}
                        </span>
                    </div>`;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}


function sendMessage() {
    const messageInput = document.getElementById("messageInput");
    const message = messageInput.value.trim(); // Trim whitespace

    if (message === "") return; // Prevent sending empty messages

    fetch("send_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `receiver_id=${chatWith}&message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = ""; // Clear input after sending
            fetchMessages(); // Refresh messages
        }
    });
}

    setInterval(fetchMessages, 2000);
</script>
