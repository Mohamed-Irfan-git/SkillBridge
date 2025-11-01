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
    <h2 class="text-center mb-4" style="color:#00b894;">📝 My Tasks</h2>
    <div class="row g-4">
        <?php if(empty($tasks)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">You haven't created any tasks yet.</div>
            </div>
        <?php else: ?>
            <?php foreach($tasks as $task): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="task-card p-4">
                        <h5 class="task-title"><?= htmlspecialchars($task['title']); ?></h5>
                        <p class="task-desc"><?= nl2br(htmlspecialchars($task['description'])); ?></p>
                        <p class="mb-1"><strong>Skills:</strong> <?= htmlspecialchars($task['skill_required']); ?></p>
                        <p class="mb-1"><strong>Deadline:</strong> <?= $task['deadline']; ?></p>
                        <p class="mb-1">
                            <strong>Status:</strong>
                            <?php if($task['status']=='open'): ?>
                                <span class="badge bg-info">Open</span>
                            <?php elseif($task['status']=='assigned'): ?>
                                <span class="badge bg-success">Assigned</span>
                            <?php elseif($task['status']=='completed'): ?>
                                <span class="badge bg-secondary">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-warning"><?= ucfirst($task['status']); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="mb-3"><strong>Applications:</strong> <?= $task['applications_count']; ?></p>

                        <?php
                        // Fetch all applications for this task
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
                                <label class="form-label">Assign Freelancer</label>
                                <select class="form-select assign-select" data-task-id="<?= $task['task_id']; ?>">
                                    <option value="">-- Select Freelancer --</option>
                                    <?php foreach($applications as $app): ?>
                                        <option value="<?= $app['freelancer_id']; ?>" <?= $app['status']=='accepted'?'selected disabled':''; ?>>
                                            <?= htmlspecialchars($app['name']); ?> (<?= ucfirst($app['status']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php elseif(empty($applications)): ?>
                            <p class="text-muted">No applications yet.</p>
                        <?php endif; ?>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="edit_task.php?task_id=<?= $task['task_id']; ?>" class="btn btn-outline-primary flex-fill">Edit</a>

                            <?php if($task['status']=='assigned'): ?>
                                <a href="mark_completed.php?task_id=<?= $task['task_id']; ?>" class="btn btn-success flex-fill" onclick="return confirm('Are you sure this task is completed?')">Mark Completed</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
        color: #00b894;
    }
    .task-desc {
        font-size: 0.95rem;
        color: #555;
        height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .assign-select {
        border-radius: 10px;
        padding: 6px;
    }
</style>

<script>
    document.querySelectorAll('.assign-select').forEach(select => {
        select.addEventListener('change', function(){
            const taskId = this.dataset.taskId;
            const freelancerId = this.value;
            if(freelancerId){
                if(confirm("Are you sure you want to assign this task?")){
                    fetch('assign_freelancer.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `task_id=${taskId}&freelancer_id=${freelancerId}`
                    })
                        .then(res => res.text())
                        .then(data => {
                            alert(data);
                            if(data.trim() === 'success'){
                                location.reload();
                            }
                        })
                        .catch(err => alert('Error assigning task.'));
                }
            }
        });
    });
</script>

<?php require '../includes/footer.php'; ?>
