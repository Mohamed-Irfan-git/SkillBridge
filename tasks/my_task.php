<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check for success message
$success_msg = '';
if(isset($_GET['success']) && $_GET['success'] == '1'){
    $success_msg = "Task marked as completed!";
}

// Fetch tasks created by this user
$stmt = $conn->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM applications a WHERE a.task_id=t.task_id) as applications_count
    FROM tasks t
    WHERE t.user_id=?
    ORDER BY t.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container my-5">
    <h2 class="text-center mb-5 text-primary">📝 My Tasks</h2>

    <?php if($success_msg): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if(empty($tasks)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    You haven't created any tasks yet.
                </div>
            </div>
        <?php else: ?>
            <?php foreach($tasks as $task): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="task-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="task-title"><?= htmlspecialchars($task['title']); ?></h5>
                            <?php
                            $status = $task['status'];
                            $badge = $status=='open' ? 'bg-info' : ($status=='assigned' ? 'bg-success' : ($status=='completed' ? 'bg-secondary' : 'bg-warning'));
                            ?>
                            <span class="badge <?= $badge ?>"><?= ucfirst($status); ?></span>
                        </div>

                        <p class="task-desc"><?= nl2br(htmlspecialchars($task['description'])); ?></p>

                        <ul class="list-unstyled mb-3">
                            <li><strong>Skills:</strong> <?= htmlspecialchars($task['skill_required']); ?></li>
                            <li><strong>Deadline:</strong> <?= $task['deadline']; ?></li>
                            <li><strong>Applications:</strong> <?= $task['applications_count']; ?></li>
                        </ul>

                        <?php
                        // Fetch applications
                        $stmt2 = $conn->prepare("
                            SELECT a.*, u.name 
                            FROM applications a 
                            JOIN users u ON a.freelancer_id=u.user_id
                            WHERE a.task_id=? ORDER BY a.applied_at DESC
                        ");
                        $stmt2->bind_param("i", $task['task_id']);
                        $stmt2->execute();
                        $applications = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt2->close();
                        ?>

                        <?php if(!empty($applications) && $task['status'] != 'completed'): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign Freelancer</label>
                                <form method="POST" action="./assign_freelancer.php">
                                    <input type="hidden" name="task_id" value="<?= $task['task_id']; ?>">
                                    <select class="form-select assign-select" name="freelancer_id" required onchange="this.form.submit()">
                                        <option value="">-- Select Freelancer --</option>
                                        <?php foreach($applications as $app): ?>
                                            <option value="<?= $app['freelancer_id']; ?>" <?= $app['status']=='accepted'?'selected disabled':''; ?>>
                                                <?= htmlspecialchars($app['name']); ?> (<?= ucfirst($app['status']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>
                        <?php elseif(empty($applications)): ?>
                            <p class="text-muted">No applications yet.</p>
                        <?php endif; ?>

                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <a href="edit_task.php?task_id=<?= $task['task_id']; ?>" class="btn btn-outline-primary flex-fill">Edit</a>

                            <?php if($task['status']=='assigned'): ?>
                                <form method="POST" action="mark_completed.php" class="flex-fill" onsubmit="return confirm('Are you sure this task is completed?');">
                                    <input type="hidden" name="task_id" value="<?= $task['task_id']; ?>">
                                    <button type="submit" class="btn btn-success w-100">Mark Completed</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .task-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 25px; transition: 0.3s; }
    .task-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
    .task-title { font-weight:700; color:#00b894; font-size:1.25rem; }
    .task-desc { font-size:0.95rem; color:#555; min-height:60px; overflow:hidden; text-overflow:ellipsis; }
    .badge { font-weight:600; text-transform:capitalize; font-size:0.85rem; padding:0.35em 0.7em; }
    .btn { border-radius:12px; font-weight:500; transition:all 0.3s ease; }
    .btn-outline-primary { color:#00b894; border-color:#00b894; }
    .btn-outline-primary:hover { background:#00b894; color:#fff; }
    .assign-select { border-radius:10px; padding:6px; }
    @media (max-width:768px){ .task-card{padding:20px;} }
</style>

<?php require '../includes/footer.php'; ?>
