<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$freelancer_id = $_SESSION['user_id'];

// Fetch tasks assigned to this freelancer
$stmt = $conn->prepare("
    SELECT t.*, u.name AS creator_name, a.status AS app_status
    FROM tasks t
    JOIN applications a ON t.task_id = a.task_id AND a.freelancer_id = ?
    JOIN users u ON t.user_id = u.user_id
    WHERE a.status='accepted'
    ORDER BY t.deadline ASC
");
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// If no tasks assigned, show an alert
if (empty($tasks)) {
    echo '<script>alert("You are not authorized or have no assigned tasks."); window.location.href="../index.php";</script>';
    exit;
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4" style="color:#00b074;">📌 My Assigned Tasks</h2>
    <div class="row g-4">
        <?php foreach ($tasks as $task): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card task-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-success"><?= htmlspecialchars($task['title']); ?></h5>
                        <p class="card-text task-desc"><?= nl2br(htmlspecialchars($task['description'])); ?></p>
                        <p class="mb-1"><strong>Skills:</strong> <?= htmlspecialchars($task['skill_required']); ?></p>
                        <p class="mb-1"><strong>Deadline:</strong> <?= $task['deadline']; ?></p>
                        <p class="mb-1"><strong>Created By:</strong> <?= htmlspecialchars($task['creator_name']); ?></p>
                        <p class="mb-1"><strong>Status:</strong>
                            <?php if ($task['status'] == 'assigned'): ?>
                                <span class="badge bg-success">Assigned</span>
                            <?php elseif ($task['status'] == 'completed'): ?>
                                <span class="badge bg-secondary">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-info"><?= ucfirst($task['status']); ?></span>
                            <?php endif; ?>
                        </p>

                        <div class="mt-auto d-flex gap-2 flex-wrap">
                            <?php if ($task['status'] != 'completed'): ?>
                                <a href="mark_completed.php?task_id=<?= $task['task_id']; ?>" class="btn btn-success flex-fill">Mark Completed</a>
                            <?php endif; ?>
                            <a href="view_task.php?task_id=<?= $task['task_id']; ?>" class="btn btn-outline-primary flex-fill">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .task-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.18);
    }
    .task-title {
        font-weight: 600;
        color: #00b074;
    }
    .task-desc {
        font-size: 0.95rem;
        color: #555;
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
</style>

<?php require '../includes/footer.php'; ?>
