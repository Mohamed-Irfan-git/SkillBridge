<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT notif_id, message, status, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mark all as read after viewing
$updateStmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE user_id = ?");
$updateStmt->bind_param("i", $user_id);
$updateStmt->execute();
$updateStmt->close();
?>

<div class="container mt-5 pt-5">
    <h2 class="text-light mb-4">Your Notifications</h2>
    <div class="list-group">
        <?php 
        $hasNotifications = false;
        while($row = $result->fetch_assoc()): 
            $hasNotifications = true;
        ?>
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
        
        <?php if (!$hasNotifications): ?>
            <div class="list-group-item text-center text-muted">
                <h5>No notifications yet</h5>
                <p>You'll see notifications here when someone applies to your tasks or when you get assigned to tasks.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

