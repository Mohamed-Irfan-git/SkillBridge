<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo "error: Not logged in";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method";
    exit();
}

$task_id = intval($_POST['task_id']);
$user_id = $_SESSION['user_id'];

// Verify the task belongs to the user
$stmt = $conn->prepare("SELECT user_id FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "error: Task not found";
    exit();
}

$task = $result->fetch_assoc();
if ($task['user_id'] != $user_id) {
    echo "error: You can only delete your own tasks";
    exit();
}

// Delete related records first (due to foreign key constraints)
$conn->query("DELETE FROM applications WHERE task_id = $task_id");
$conn->query("DELETE FROM messages WHERE task_id = $task_id");

// Delete the task
$deleteStmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
$deleteStmt->bind_param("i", $task_id);

if ($deleteStmt->execute()) {
    echo "success";
} else {
    echo "error: Failed to delete task";
}

$deleteStmt->close();
$conn->close();
?>
