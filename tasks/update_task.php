<?php
global $conn;
session_start();
require '../config/db_connection.php';

if(!isset($_SESSION['user_id'], $_POST['task_id'], $_POST['status'])){
    echo "error";
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = (int)$_POST['task_id'];
$status = $_POST['status'];

// Validate status
if(!in_array($status, ['open','assigned','complete'])){
    echo "error";
    exit;
}

// Only creator can update their task
$stmt = $conn->prepare("UPDATE tasks SET status=? WHERE task_id=? AND user_id=?");
$stmt->bind_param("sii", $status, $task_id, $user_id);
if($stmt->execute()){
    echo "success";
}else{
    echo "error";
}
$stmt->close();

