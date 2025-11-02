<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

global $conn;
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_POST['task_id'])) {
    header("Location: my_task.php");
    exit;
}

$task_id = (int)$_POST['task_id'];
$user_id = $_SESSION['user_id'];

// Check ownership
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id=? AND user_id=?");
if (!$stmt) { die("Prepare failed: ".$conn->error); }
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    die("You are not authorized to update this task.");
}

if ($task['status'] != 'assigned') {
    die("Task cannot be marked as completed unless it is assigned.");
}

// Update task status
$stmt2 = $conn->prepare("UPDATE tasks SET status='completed' WHERE task_id=?");
if (!$stmt2) { die("Prepare failed: ".$conn->error); }
$stmt2->bind_param("i", $task_id);
$stmt2->execute();
$stmt2->close();

// Update freelancer's application status
$stmt3 = $conn->prepare("UPDATE applications SET status='completed' WHERE task_id=? AND status='accepted'");
if (!$stmt3) { die("Prepare failed: ".$conn->error); }
$stmt3->bind_param("i", $task_id);
$stmt3->execute();
$stmt3->close();

// Redirect to my_task.php with success
header("Location: my_task.php?success=1");
exit();
