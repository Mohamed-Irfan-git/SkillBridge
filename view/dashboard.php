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
    body {
        background-color: #0b132b;
        color: #f8f9fa;
    }

    .dashboard-header {
        background: linear-gradient(90deg, #0d6efd, #66b2ff);
        color: white;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        position: relative;
    }

    .profile-avatar {
        width: 90px;
        height: 90px;
        background-color: white;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        font-size: 32px;
        font-weight: bold;
        margin-right: 15px;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .edit-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        color: white;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
    }

    .edit-icon:hover {
        color: #ffe066;
        transform: scale(1.2);
    }

    @media (max-width: 576px) {
        .profile-avatar {
            margin: 0 auto 10px;
        }
    }

    .section-title {
        font-weight: 600;
        margin-bottom: 15px;
        color: #ffffff;
        border-bottom: 2px solid #0d6efd;
        display: inline-block;
        padding-bottom: 5px;
    }

    .task-card {
        background: #1c2541;
        color: #f8f9fa;
        border: none;
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 20px rgba(13, 110, 253, 0.2);
    }

    .tasknot {
        font-size: 18px;
        font-weight: 500;
        color: #d0d0d0;
    }

    .btn {
        border-radius: 8px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .action-buttons a {
        background-color: #1c2541;
        color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        transition: 0.3s;
        text-decoration: none;
    }

    .action-buttons a:hover {
        background-color: #0d6efd;
        color: white;
    }

    .points-box {
        background-color: rgba(255,255,255,0.15);
        padding: 10px 15px;
        border-radius: 10px;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
    }

</style>

<div class="container py-5">

    <!-- Header -->
    <div class="dashboard-header">
        <div class="d-flex align-items-center flex-wrap">
            <div class="profile-avatar position-relative">
                <?php if (!empty($userProfile['profile_photo'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($userProfile['profile_photo']) ?>" alt="Profile">
                <?php else: ?>
                    <?= strtoupper(substr($userProfile['name'], 0, 1)) ?>
                <?php endif; ?>
                <a href="../profile/edit_photo.php" class="edit-icon" title="Edit Profile Photo"><i class="fas fa-pencil-alt"></i></a>
            </div>
            <div>
                <h4 class="mb-1">
                    Welcome back, <strong><?= htmlspecialchars($userProfile['name']) ?></strong> 👋
                    <a href="../profile/edit_name.php" class="text-white ms-2" title="Edit Name"><i class="fas fa-pencil-alt edit-icon" style="position: static; color: #fff;"></i></a>
                </h4>
                <p class="mb-0">
                    Role: <?= htmlspecialchars($userProfile['role'] ?? 'Freelancer') ?> |
                    Email: <?= htmlspecialchars($userProfile['email']) ?>
                    <a href="../profile/change_email.php" title="Change Email"><i class="fas fa-pencil-alt edit-icon" style="position: static; margin-left:5px;"></i></a>
                </p>
                <p class="mt-2">Bio: <?= htmlspecialchars($userProfile['bio'] ?? 'No bio added yet.') ?>
                    <a href="../profile/edit_bio.php" title="Edit Bio"><i class="fas fa-pencil-alt edit-icon" style="position: static; margin-left:5px;"></i></a>
                </p>
            </div>
        </div>

        <!-- Points Section -->
        <div class="points-box mt-3 mt-md-0">
            ⭐ Points: <?= htmlspecialchars($userProfile['points'] ?? 0) ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="../tasks/add_task.php" title="Add New Task"><i class="fas fa-plus"></i> Add Task</a>
            <a href="../profile/change_password.php" title="Change Password"><i class="fas fa-lock"></i> Change Password</a>
            <a href="../tasks/assign_task.php" title="Assign New Task"><i class="fas fa-tasks"></i> Assign Task</a>
        </div>
    </div>

    <!-- My Created Tasks -->
    <h4 class="section-title">My Created Tasks</h4>
    <div class="row g-4 pt-3">
        <?php if ($myTasks): ?>
            <?php foreach ($myTasks as $task): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card task-card p-4 h-100">
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

                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-primary flex-fill">Chat</a>
                            <a href="../tasks/edit_task.php?task_id=<?= $task['task_id'] ?>" class="btn btn-warning btn-sm flex-fill">Edit</a>
                            <button class="btn btn-danger btn-sm flex-fill delete-task-btn" data-task-id="<?= $task['task_id'] ?>">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center mt-3"><span class="tasknot">You haven’t created any tasks yet.</span></p>
        <?php endif; ?>
    </div>

    <!-- Assigned Tasks -->
    <h4 class="section-title mt-5">Tasks Assigned to Me</h4>
    <div class="row g-4 pt-3">
        <?php if ($assignedTasks): ?>
            <?php foreach ($assignedTasks as $task): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card task-card p-4 h-100">
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
            <p class="text-center mt-3"><span class="tasknot">You are not assigned to any tasks yet.</span></p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Update task status
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

    // Delete task
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
