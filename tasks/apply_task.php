<?php
session_start();
require '../config/db_connection.php';

if(isset($_POST['task_id']) && isset($_SESSION['user_id'])){
    $task_id = $_POST['task_id'];
    $freelancer_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO applications(task_id, freelancer_id) VALUES(?,?)");
    $stmt->bind_param("ii",$task_id,$freelancer_id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_tasks.php");
}
?>
