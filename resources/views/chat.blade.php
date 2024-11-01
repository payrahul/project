<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        let currentUserId = null; // Changed from currentGroup to currentUserId for one-to-one chat
        console.log(1)

        // Function to switch to a specific user chat
        function switchUser(userId) {
            console.log(2)
            currentUserId = userId; // Set the current user to chat with
            fetchMessages(); // Fetch messages for the selected user
            console.log(3)
        }

        // Fetch messages for the current user
        function fetchMessages() {
            console.log(4)
            if (currentUserId) { // Ensure a user is selected
                console.log(5)
                fetch('/get-messages?user_id=' + currentUserId) // Fetch messages for the selected user
                    .then(response => response.json())
                    .then(data => {
                        console.log(5)
                        const messagesContainer = document.getElementById('messages');
                        messagesContainer.innerHTML = ''; // Clear existing messages
                        data.forEach(msg => {
                            messagesContainer.innerHTML += `<p><strong>${msg.user.name}:</strong> ${msg.message}</p>`; // Display message
                        });
                    })
                    .catch(error => console.error('Error fetching messages:', error));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Pusher
            console.log(6)
            switchUser();
            const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
            });
            console.log(6)
            const channel = pusher.subscribe('chat.global');
            channel.bind('message.sent', function(data) {
                if (data.user_id === currentUserId) { // Check if the message is for the current user
                    console.log(8)
                    fetchMessages(); // Fetch messages to update the chat
                }
            });

            // Sending a message
            document.getElementById('send').onclick = function() {
                console.log(9)
                const message = document.getElementById('message').value; // Get the message from input
                if (message.trim() !== '') { // Ensure message is not empty
                    console.log(10)
                    fetch('/send-message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: message, user_id: currentUserId }) // Send message to server
                    })
                    .then(() => {
                        document.getElementById('message').value = ''; // Clear the message input
                    })
                    .catch(error => console.error('Error sending message:', error));
                }
            };

            // Optional: Here you might want to add functionality to select users
            // This is commented out since we're focusing on one-to-one chat only
            // document.getElementById('select-user').onclick = function() {
            //     const userId = document.getElementById('user_id').value; // Get the selected user ID
            //     switchUser(userId); // Switch to the selected user
            // };
        });
    </script>
</head>
<body>
    
    <div id="chat-container">
        <div id="messages" style="border: 1px solid #ccc; height: 300px; overflow-y: scroll; margin-bottom: 10px;"></div>
        <input type="text" id="message" placeholder="Your Message">
        <button id="send">Send</button>
        <!-- Removed group creation functionality for one-to-one chat -->
    </div>
</body>
</html>
