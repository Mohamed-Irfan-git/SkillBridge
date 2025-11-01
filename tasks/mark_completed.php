<?php

global $conn;
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['task_id'])) {
    header("Location: tasks.php");
    exit;
}

$task_id = (int)$_GET['task_id'];
$user_id = $_SESSION['user_id'];

// Check if this task belongs to the current user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id=? AND user_id=?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$task) {
    echo "You are not authorized to update this task.";
    exit;
}

// Only allow completing if task is assigned
if ($task['status'] != 'assigned') {
    echo "Task cannot be marked as completed unless it is assigned.";
    exit;
}

// Update task status to completed
$stmt2 = $conn->prepare("UPDATE tasks SET status='completed' WHERE task_id=?");
$stmt2->bind_param("i", $task_id);
if ($stmt2->execute()) {
    $stmt2->close();
    // Optional: update the freelancer's application status to completed
    $stmt3 = $conn->prepare("UPDATE applications SET status='completed' WHERE task_id=? AND status='accepted'");
    $stmt3->bind_param("i", $task_id);
    $stmt3->execute();
    $stmt3->close();

    header("Location: tasks.php?success=Task marked as completed");
    exit;
} else {
    echo "Failed to update task. Try again.";
}

