<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'], $_POST['task_id'], $_POST['message'])) exit();

$user_id = $_SESSION['user_id'];
$task_id = (int)$_POST['task_id'];
$message = trim($_POST['message']);

if ($message === '') exit();

$stmt = $conn->prepare("INSERT INTO messages (task_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $task_id, $user_id, $message);
$stmt->execute();
$stmt->close();
?>
