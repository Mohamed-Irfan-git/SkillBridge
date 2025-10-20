<?php
global $conn;
session_start();
require '../config/db_connection.php';

if(!isset($_SESSION['user_id'])){
    echo "Unauthorized access";
    exit;
}

if(!isset($_POST['task_id'], $_POST['freelancer_id'])){
    echo "Invalid data";
    exit;
}

$task_id = (int)$_POST['task_id'];
$freelancer_id = (int)$_POST['freelancer_id'];
$creator_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id=? AND user_id=?");
$stmt->bind_param("ii", $task_id, $creator_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$task){
    echo "You are not authorized to assign this task.";
    exit;
}

if($task['status'] === 'complete'){
    echo "Cannot assign a completed task.";
    exit;
}

$stmt = $conn->prepare("UPDATE applications SET status='rejected' WHERE task_id=? AND freelancer_id<>?");
$stmt->bind_param("ii", $task_id, $freelancer_id);
$stmt->execute();
$stmt->close();

$stmt2 = $conn->prepare("UPDATE applications SET status='accepted' WHERE task_id=? AND freelancer_id=?");
$stmt2->bind_param("ii", $task_id, $freelancer_id);
$stmt2->execute();
$stmt2->close();

$stmt3 = $conn->prepare("UPDATE tasks SET status='assigned' WHERE task_id=?");
$stmt3->bind_param("i", $task_id);
$stmt3->execute();
$stmt3->close();

echo "success";

