<?php
global $conn;
session_start();
require '../config/db_connection.php';

if(isset($_POST['task_id']) && isset($_SESSION['user_id'])){
    $task_id = (int)$_POST['task_id'];
    $freelancer_id = $_SESSION['user_id'];

    // Insert application
    $stmt = $conn->prepare("INSERT INTO applications(task_id, freelancer_id) VALUES(?, ?)");
    $stmt->bind_param("ii", $task_id, $freelancer_id);
    $stmt->execute();
    $stmt->close();

    // Get the task creator ID
    $taskStmt = $conn->prepare("SELECT user_id FROM tasks WHERE task_id = ?");
    $taskStmt->bind_param("i", $task_id);
    $taskStmt->execute();
    $result = $taskStmt->get_result();
    $task = $result->fetch_assoc();
    $creator_id = $task['user_id'] ?? null;
    $taskStmt->close();

    // Get freelancer name
    $userStmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $userStmt->bind_param("i", $freelancer_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $freelancer = $userResult->fetch_assoc();
    $freelancer_name = $freelancer['name'] ?? 'Someone';
    $userStmt->close();

    // Insert notification for the creator
    if($creator_id && $creator_id != $freelancer_id){
        $notif_message = "$freelancer_name applied for your task.";
        $notif = $conn->prepare("INSERT INTO notifications (user_id, message, status) VALUES (?, ?, 'unread')");
        $notif->bind_param("is", $creator_id, $notif_message);
        $notif->execute();
        $notif->close();
    }

    // Redirect back with a success flag
    header("Location: ./view_task.php?applied=1");
    exit;
}
?>
