<?php 
include 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$loggedInUser = $_SESSION['user_id']; // Current user
$chatWith = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
if ($chatWith === 0) {
    echo "<p class='text-red-500 text-center'>Invalid User</p>";
    exit();
}
?>

<div class="container mx-auto p-6"><br>
    <div class="max-w-lg mx-auto bg-white p-4 rounded-lg shadow-lg relative">
        <!-- Back Button -->
        <a href="messagelist.php" class="absolute top-4 left-4 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>

        <h2 class="text-xl font-semibold text-gray-700 text-center mb-4">Chat</h2>
        <div id="chatBox" class="h-80 overflow-y-auto border p-3 rounded mb-3 bg-gray-100"></div>
        <div class="flex">
            <input type="text" id="messageInput" class="w-full border rounded p-2" placeholder="Type a message...">
            <button onclick="sendMessage()" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Send</button>
        </div>
    </div>
</div>


<script>
    const chatWith = <?php echo $chatWith; ?>;
    const loggedInUser = <?php echo $loggedInUser; ?>;

    function fetchMessages() {
        fetch(`fetch_messages.php?chat_with=${chatWith}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const chatBox = document.getElementById("chatBox");
                    chatBox.innerHTML = "";
                    data.messages.forEach(msg => {
                        const isSender = msg.sender_id == loggedInUser;
                        chatBox.innerHTML += `<div class="p-2 mb-2 ${isSender ? 'text-right' : 'text-left'}">
                            <span class="inline-block px-3 py-1 rounded ${isSender ? 'bg-green-500 text-white' : 'bg-gray-300 text-black'}">
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

    // Fetch messages every 2 seconds
    setInterval(fetchMessages, 2000);
    fetchMessages();
</script>
