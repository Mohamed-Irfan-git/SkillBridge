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

// ---------- Fetch User Profile ----------
$profileSql = "SELECT * FROM users WHERE user_id=?";
$stmtProfile = $conn->prepare($profileSql);
$stmtProfile->bind_param("i", $user_id);
$stmtProfile->execute();
$userProfile = $stmtProfile->get_result()->fetch_assoc();
$stmtProfile->close();

// ---------- My Tasks ----------
$myTasksSql = "SELECT t.*, 
                      (SELECT COUNT(*) FROM applications a WHERE a.task_id=t.task_id AND a.status='accepted') AS assigned_count
               FROM tasks t
               WHERE t.user_id=?";
$stmt = $conn->prepare($myTasksSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myTasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Assigned Tasks ----------
$assignedSql = "SELECT t.*, u.name AS creator_name
                FROM tasks t
                JOIN applications a ON t.task_id=a.task_id
                JOIN users u ON t.user_id=u.user_id
                WHERE a.freelancer_id=? AND a.status='accepted'";
$stmt2 = $conn->prepare($assignedSql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$assignedTasks = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
?>

<style>
    .dashboard-header {
        background: linear-gradient(90deg, #0d6efd, #66b2ff);
        color: white;
        padding: 25px 20px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .task-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
    }

    .task-status {
        font-weight: 500;
        text-transform: capitalize;
    }

    .section-title {
        font-weight: 600;
        margin-bottom: 15px;
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd;
        display: inline-block;
        padding-bottom: 5px;
    }
</style>

<div class="container" style="padding-top:100px;">

    <!-- Header -->
    <div class="dashboard-header d-flex align-items-center">
        <div style="width:80px; height:80px; background-color:white; border-radius:50%; 
                display:flex; align-items:center; justify-content:center; color:#0d6efd; font-size:36px; font-weight:bold; margin-right:15px;">
            <?= strtoupper(substr($userProfile['name'], 0, 1)) ?>
        </div>
        <div>
            <h4 class="mb-1">Welcome back, <strong><?= htmlspecialchars($userProfile['name']) ?></strong> 👋</h4>
            <p class="mb-0">Role: <?= htmlspecialchars($userProfile['role'] ?? 'Freelancer') ?> | 
                Email: <?= htmlspecialchars($userProfile['email']) ?></p>
        </div>
    </div>

    <!-- My Tasks -->
    <h4 class="section-title">My Created Tasks</h4>
    <div class="row pt-3">
        <?php if ($myTasks): ?>
            <?php foreach ($myTasks as $task): ?>
                <div class="col-md-4 mb-4">
                    <div class="card task-card p-4 shadow-sm border-0">
                        <h5 class="text-primary"><?= htmlspecialchars($task['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($task['description'], 0, 100)) ?>...</p>
                        <p><strong>Skill:</strong> <?= htmlspecialchars($task['skill_required']) ?></p>
                        <p><strong>Deadline:</strong> <?= htmlspecialchars($task['deadline']) ?></p>
                        <p><strong>Freelancers:</strong> <?= $task['assigned_count'] ?></p>

                        <label>Status:</label>
                        <select class="form-select form-select-sm mb-2 task-status" data-task-id="<?= $task['task_id'] ?>">
                            <option value="open" <?= $task['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="assigned" <?= $task['status'] == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                            <option value="complete" <?= $task['status'] == 'complete' ? 'selected' : '' ?>>Complete</option>
                        </select>

                        <div class="d-flex gap-2 mt-2">
                            <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-primary flex-fill">Chat</a>
                            <a href="../tasks/edit_task.php?task_id=<?= $task['task_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm delete-task-btn" data-task-id="<?= $task['task_id'] ?>">Delete</button>
                        </div>

                        <?php if ($task['status'] != 'complete'): ?>
                            <div class="mt-3">
                                <h6>Applications</h6>
                                <?php
                                $appsStmt = $conn->prepare("SELECT a.*, u.name AS freelancer_name FROM applications a JOIN users u ON a.freelancer_id=u.user_id WHERE a.task_id=?");
                                $appsStmt->bind_param("i", $task['task_id']);
                                $appsStmt->execute();
                                $applications = $appsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                $appsStmt->close();
                                ?>
                                <?php foreach ($applications as $app): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span><?= htmlspecialchars($app['freelancer_name']) ?> (<?= ucfirst($app['status']) ?>)</span>
                                        <?php if ($app['status'] == 'pending'): ?>
                                            <button class="btn btn-success btn-sm assign-btn"
                                                data-task="<?= $task['task_id'] ?>"
                                                data-freelancer="<?= $app['freelancer_id'] ?>">
                                                Assign
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">You haven’t created any tasks yet.</p>
        <?php endif; ?>
    </div>

    <!-- Assigned Tasks -->
    <h4 class="section-title mt-5">Tasks Assigned to Me</h4>
    <div class="row pt-3">
        <?php if ($assignedTasks): ?>
            <?php foreach ($assignedTasks as $task): ?>
                <div class="col-md-4 mb-4">
                    <div class="card task-card p-4 shadow-sm border-0">
                        <h5 class="text-primary"><?= htmlspecialchars($task['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($task['description'], 0, 100)) ?>...</p>
                        <p><strong>Skill:</strong> <?= htmlspecialchars($task['skill_required']) ?></p>
                        <p><strong>Deadline:</strong> <?= htmlspecialchars($task['deadline']) ?></p>
                        <p><strong>Creator:</strong> <?= htmlspecialchars($task['creator_name']) ?></p>

                        <span class="badge 
                            <?= $task['status'] == 'open' ? 'bg-info' : ($task['status'] == 'assigned' ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= ucfirst($task['status']) ?>
                        </span>

                        <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-outline-primary mt-3 w-100">View & Chat</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">You are not assigned to any tasks yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.task-status').forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const status = this.value;

            fetch('../tasks/update_task.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'task_id=' + taskId + '&status=' + status
            })
            .then(res => res.text())
            .then(resp => {
                if (resp === 'success') {
                    alert('✅ Task status updated successfully!');
                } else {
                    alert('❌ Error updating task status.');
                }
            });
        });
    });

    document.querySelectorAll('.assign-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.task;
            const freelancerId = this.dataset.freelancer;

            if (confirm("Assign this freelancer to the task?")) {
                fetch('../tasks/assign_freelancer.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'task_id=' + taskId + '&freelancer_id=' + freelancerId
                })
                .then(res => res.text())
                .then(resp => {
                    if (resp === 'success') {
                        alert('🎉 Freelancer assigned!');
                        location.reload();
                    } else {
                        alert(resp);
                    }
                });
            }
        });
    });

    document.querySelectorAll('.delete-task-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;

            if (confirm("Delete this task permanently?")) {
                fetch('../tasks/delete_task.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'task_id=' + taskId
                })
                .then(res => res.text())
                .then(resp => {
                    if (resp === 'success') {
                        alert('🗑️ Task deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + resp);
                    }
                });
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
