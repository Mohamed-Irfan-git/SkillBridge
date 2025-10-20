<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT notif_id, message, status, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mark all as read after viewing
$conn->query("UPDATE notifications SET status = 'read' WHERE user_id = $user_id");
?>

<div class="container mt-5 pt-5">
    <h2 class="text-light mb-4">Your Notifications</h2>
    <div class="list-group">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <?= htmlspecialchars($row['message']) ?>
                    <div><small class="text-muted"><?= $row['created_at'] ?></small></div>
                </div>
                <?php if($row['status'] == 'unread'): ?>
                    <span class="badge bg-primary">New</span>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

