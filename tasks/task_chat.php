<?php
global $conn;
session_start();
require '../config/db_connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['task_id'] ?? null;

if(!$task_id){
    echo "No task selected";
    exit;
}

// Check if user can access this task
$checkSql = "SELECT * FROM tasks t 
             LEFT JOIN applications a ON t.task_id=a.task_id
             WHERE t.task_id=? AND (t.user_id=? OR (a.freelancer_id=? AND a.status='accepted'))";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("iii", $task_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    echo "Unauthorized access to chat";
    exit;
}
$task = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task Chat</title>
<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #ece5dd;
}
.chat-container {
    width: 100%;
    height: 100vh;
    display: flex;
    flex-direction: column;
}
.chat-header {
    background-color: #075E54;
    color: white;
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
}
.chat-box {
    flex: 1;
    background-color: #e5ddd5;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
}
.chat-message {
    max-width: 70%;
    margin-bottom: 10px;
    border-radius: 8px;
    padding: 8px 12px;
    position: relative;
    word-wrap: break-word;
    display: inline-block;
}
.sender {
    background-color: #DCF8C6;
    align-self: flex-end;
    text-align: right;
}
.receiver {
    background-color: white;
    align-self: flex-start;
}
.chat-message strong {
    display: block;
    font-size: 12px;
    color: #555;
    margin-bottom: 3px;
}
.chat-input {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f0f0f0;
    border-top: 1px solid #ccc;
}
.chat-input textarea {
    flex: 1;
    border: none;
    border-radius: 20px;
    padding: 10px 15px;
    resize: none;
    height: 45px;
    outline: none;
    font-size: 14px;
}
.chat-input button {
    background-color: #075E54;
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    margin-left: 8px;
    cursor: pointer;
    font-size: 18px;
}
.chat-input button:hover {
    background-color: #0b7d6e;
}
</style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">💬 <?= htmlspecialchars($task['title']) ?></div>
    <div id="chat-box" class="chat-box"></div>

    <div class="chat-input">
        <textarea id="message" placeholder="Type a message..."></textarea>
        <button id="send-btn">➤</button>
    </div>
</div>

<script>
const taskId = <?= $task_id ?>;
const chatBox = document.getElementById("chat-box");
const sendBtn = document.getElementById("send-btn");
const messageInput = document.getElementById("message");

// Load messages
function loadMessages() {
    fetch(`./load_messages.php?task_id=${taskId}`)
        .then(res => res.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Send message
sendBtn.addEventListener("click", () => {
    const message = messageInput.value.trim();
    if (message === "") return;

    const formData = new FormData();
    formData.append("task_id", taskId);
    formData.append("message", message);

    fetch("./send_messages.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        messageInput.value = "";
        loadMessages();
    });
});

// Auto refresh
setInterval(loadMessages, 2000);
loadMessages();
</script>

</body>
</html>
