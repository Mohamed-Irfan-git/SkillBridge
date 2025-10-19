<?php
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

// ---------- Assigned Tasks (I am freelancer) ----------
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

<div class="container" style="padding-top:100px;">

    <div class="card mb-4 shadow-sm p-3 d-flex align-items-center flex-row"
        style="background-color: #e3f2fd; border-radius: 12px;">

        <!-- Round Icon -->
        <div style="width:80px; height:80px; background-color:#0d6efd; border-radius:50%; 
                display:flex; align-items:center; justify-content:center; color:white; font-size:36px; font-weight:bold; margin-right:15px;">
            <?= strtoupper(substr($userProfile['name'], 0, 1)) ?>
        </div>

        <div>
            <h5 class="mb-1" style="color:#0d6efd; font-weight:600;"><?= htmlspecialchars($userProfile['name']) ?></h5>
            <p class="mb-0" style="color:#495057;"><strong>Email:</strong> <?= htmlspecialchars($userProfile['email']) ?></p>
            <p class="mb-0" style="color:#495057;"><strong>Role:</strong> <?= htmlspecialchars($userProfile['role'] ?? 'Freelancer') ?></p>
        </div>

    </div>
    <!-- My Tasks -->
    <h4>My Tasks</h4>
    <div class="row pt-5">
        <?php if ($myTasks): ?>
            <?php foreach ($myTasks as $task): ?>
                <div class="col-md-4">
                    <div class="card mb-5 mt-2 p-4 shadow-sm">
                        <h5 class="text-primary"><?= htmlspecialchars($task['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($task['description'], 0, 100)) ?>...</p>
                        <p><strong>Skill:</strong> <?= htmlspecialchars($task['skill_required']) ?></p>
                        <p><strong>Deadline:</strong> <?= htmlspecialchars($task['deadline']) ?></p>
                        <p><strong>Assigned Freelancers:</strong> <?= $task['assigned_count'] ?></p>

                        <!-- Task Status -->
                        <label>Status:</label>
                        <select class="form-select task-status" data-task-id="<?= $task['task_id'] ?>">
                            <option value="open" <?= $task['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                            <option value="assigned" <?= $task['status'] == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                            <option value="complete" <?= $task['status'] == 'complete' ? 'selected' : '' ?>>Complete</option>
                        </select>

                        <!-- View & Chat -->
                        <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-primary w-100 mt-2">View & Chat</a>

                        <!-- Assign Freelancers -->
                        <?php if ($task['status'] != 'complete'): ?>
                            <h6 class="mt-2">Applications</h6>
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
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">You have not created any tasks yet.</p>
        <?php endif; ?>
    </div>

    <!-- Assigned Tasks -->
    <h4>Assigned Tasks</h4>
    <div class="row pt-5">
        <?php if ($assignedTasks): ?>
            <?php foreach ($assignedTasks as $task): ?>
                <div class="col-md-4">
                    <div class="card mb-2 p-3 shadow-sm">
                        <h5 class="text-primary"><?= htmlspecialchars($task['title']) ?></h5>
                        <p><?= htmlspecialchars(substr($task['description'], 0, 100)) ?>...</p>
                        <p><strong>Skill:</strong> <?= htmlspecialchars($task['skill_required']) ?></p>
                        <p><strong>Deadline:</strong> <?= htmlspecialchars($task['deadline']) ?></p>
                        <p><strong>Creator:</strong> <?= htmlspecialchars($task['creator_name']) ?></p>

                        <span class="badge 
                    <?= $task['status'] == 'open' ? 'bg-info' : ($task['status'] == 'assigned' ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= ucfirst($task['status']) ?>
                        </span>

                        <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-primary mt-2 w-100">View & Chat</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">You are not assigned to any tasks yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Update Task Status
    document.querySelectorAll('.task-status').forEach(select => {
        select.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const status = this.value;

            fetch('../tasks/update_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'task_id=' + taskId + '&status=' + status
                })
                .then(res => res.text())
                .then(resp => {
                    if (resp === 'success') {
                        alert('Task status updated!');
                    } else {
                        alert('Error updating status');
                    }
                });
        });
    });

    // Assign Freelancer
    document.querySelectorAll('.assign-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.task;
            const freelancerId = this.dataset.freelancer;

            if (confirm("Are you sure you want to assign this freelancer?")) {
                fetch('../tasks/assign_freelancer.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'task_id=' + taskId + '&freelancer_id=' + freelancerId
                    })
                    .then(res => res.text())
                    .then(resp => {
                        if (resp === 'success') {
                            alert('Freelancer assigned!');
                            location.reload();
                        } else {
                            alert(resp);
                        }
                    });
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>