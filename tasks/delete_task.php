<?php
global $conn;
session_start();
require_once '../config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "error: Not logged in";
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "error: Invalid request method";
    exit();
}

// Check if task_id is provided
if (!isset($_POST['task_id'])) {
    echo "error: Task ID not provided";
    exit();
}

$task_id = intval($_POST['task_id']);
$user_id = $_SESSION['user_id'];

// Verify task_id is valid
if ($task_id <= 0) {
    echo "error: Invalid task ID";
    exit();
}

// Verify the task belongs to the user
$stmt = $conn->prepare("SELECT user_id FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "error: Task not found";
    $stmt->close();
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();

if ($task['user_id'] != $user_id) {
    echo "error: You can only delete your own tasks";
    exit();
}

// Delete related records first (using prepared statements for security)
// Delete applications
$deleteApps = $conn->prepare("DELETE FROM applications WHERE task_id = ?");
$deleteApps->bind_param("i", $task_id);
$deleteApps->execute();
$deleteApps->close();

// Delete messages (if messages table exists)
$deleteMessages = $conn->prepare("DELETE FROM messages WHERE task_id = ?");
$deleteMessages->bind_param("i", $task_id);
$deleteMessages->execute();
$deleteMessages->close();

// Delete the task
$deleteTask = $conn->prepare("DELETE FROM tasks WHERE task_id = ?");
$deleteTask->bind_param("i", $task_id);

if ($deleteTask->execute()) {
    echo "success";
} else {
    echo "error: Failed to delete task - " . $conn->error;
}

$deleteTask->close();
$conn->close();
?>