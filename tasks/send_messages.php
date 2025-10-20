<?php
global $conn;
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'], $_POST['task_id'], $_POST['message'])) exit();

$user_id = $_SESSION['user_id'];
$task_id = (int)$_POST['task_id'];
$message = trim($_POST['message']);

if ($message === '') exit();

// Insert message
$stmt = $conn->prepare("INSERT INTO messages (task_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $task_id, $user_id, $message);
$stmt->execute();
$stmt->close();

// Get task creator (receiver)
$taskQuery = "SELECT user_id FROM tasks WHERE task_id = ?";
$stmt = $conn->prepare($taskQuery);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$receiver_id = $task['user_id'] ?? null;
$stmt->close();

// Insert notification if the receiver is different
if ($receiver_id && $receiver_id != $user_id) {
    $notifMsg = "You received a new message for your task.";
    $notifQuery = "INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')";
    $stmt = $conn->prepare($notifQuery);
    $stmt->bind_param("is", $receiver_id, $notifMsg);
    $stmt->execute();
    $stmt->close();
}
?>
