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
    <h2 class="text-center mb-5 text-success">📌 My Assigned Tasks</h2>
    <div class="row g-4">
        <?php foreach ($tasks as $task): ?>
            <div class="col-md-6 col-lg-4">
                <div class="task-card h-100 p-4 d-flex flex-column">
                    <div class="mb-3 d-flex justify-content-between align-items-start">
                        <h5 class="task-title"><?= htmlspecialchars($task['title']); ?></h5>
                        <?php if ($task['status'] == 'assigned'): ?>
                            <span class="badge bg-success"><?= ucfirst($task['status']); ?></span>
                        <?php elseif ($task['status'] == 'completed'): ?>
                            <span class="badge bg-secondary"><?= ucfirst($task['status']); ?></span>
                        <?php else: ?>
                            <span class="badge bg-info"><?= ucfirst($task['status']); ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="task-desc"><?= nl2br(htmlspecialchars($task['description'])); ?></p>

                    <ul class="list-unstyled mb-3">
                        <li><strong>Skills:</strong> <?= htmlspecialchars($task['skill_required']); ?></li>
                        <li><strong>Deadline:</strong> <?= $task['deadline']; ?></li>
                        <li><strong>Created By:</strong> <?= htmlspecialchars($task['creator_name']); ?></li>
                    </ul>

                    <div class="mt-auto d-flex gap-2 flex-wrap">
                        <?php if ($task['status'] != 'completed'): ?>
                            <a href="mark_completed.php?task_id=<?= $task['task_id']; ?>" class="btn btn-success flex-fill">Mark Completed</a>
                        <?php endif; ?>
                        <a href="view_task.php?task_id=<?= $task['task_id']; ?>" class="btn btn-outline-primary flex-fill">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Task Card */
    .task-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    /* Task Title */
    .task-title {
        font-weight: 700;
        color: #00b074;
        font-size: 1.25rem;
    }

    /* Task Description */
    .task-desc {
        font-size: 0.95rem;
        color: #555;
        min-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Badges */
    .badge {
        font-weight: 600;
        text-transform: capitalize;
        font-size: 0.85rem;
        padding: 0.35em 0.7em;
    }

    /* Buttons */
    .btn {
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-outline-primary {
        color: #00b074;
        border-color: #00b074;
    }
    .btn-outline-primary:hover {
        background: #00b074;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .task-card { padding: 20px; }
    }
</style>

<?php require '../includes/footer.php'; ?>
