<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'], $_GET['task_id'])) exit();

$task_id = (int)$_GET['task_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT m.*, u.name 
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.task_id = ?
    ORDER BY m.created_at ASC
");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='text-center text-muted'>No messages yet.</div>";
} else {
    while ($row = $res->fetch_assoc()) {
        $class = ($row['sender_id'] == $user_id) ? 'sender' : 'receiver';
        echo "
        <div class='chat-message $class'>
            <div class='chat-name'>".htmlspecialchars($row['name'].":")."</div>
            <div class='chat-text'>".nl2br(htmlspecialchars($row['message']))."</div>
        </div>";
    }
}
$stmt->close();
?>
