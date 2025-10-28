<?php
global $conn;
session_start();
require_once '../config/db_connection.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

if (!$task_id) {
    header("Location: ../view/dashboard.php");
    exit();
}

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../view/dashboard.php");
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();

$error = '';
$success = '';

if (isset($_POST['update_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skill_required = trim($_POST['skill_required']);
    $deadline = $_POST['deadline'];

    if (empty($title) || empty($description) || empty($skill_required) || empty($deadline)) {
        $error = "All fields are required.";
    } else {
        $updateStmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, skill_required = ?, deadline = ? WHERE task_id = ? AND user_id = ?");
        $updateStmt->bind_param("ssssii", $title, $description, $skill_required, $deadline, $task_id, $user_id);

        if ($updateStmt->execute()) {
            $success = "Task updated successfully!";
            // Refresh task data
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $task = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Failed to update task. Try again.";
        }
        $updateStmt->close();
    }
}

$conn->close();
?>

<div class="container" style="max-width:600px; margin-top:120px;">
    <div class="login-card p-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); border-radius: 15px; box-shadow:0 8px 20px rgba(0,0,0,0.3);">
        <h2 class="text-center mb-4" style="font-family:'Poppins', serif; color:#00bfff; text-shadow:0 0 10px rgba(0,191,255,0.5);">Edit Task</h2>

        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php elseif($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-light">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Task Title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Describe your task..." required><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Skill Required</label>
                <input type="text" name="skill_required" class="form-control" placeholder="e.g., Web Development, Design" value="<?php echo htmlspecialchars($task['skill_required']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Deadline</label>
                <input type="date" name="deadline" class="form-control" value="<?php echo $task['deadline']; ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" name="update_task" class="btn btn-primary flex-fill" style="background: linear-gradient(90deg, #00c6ff, #0072ff); border:none; padding:10px; font-weight:500; border-radius:8px; box-shadow:0 0 10px rgba(0,212,255,0.3); transition:0.3s;">Update Task</button>
                <a href="../view/dashboard.php" class="btn btn-secondary flex-fill" style="padding:10px; font-weight:500; border-radius:8px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.login-card .form-control {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 8px;
    padding: 10px;
    transition: 0.3s;
}

.login-card .form-control:focus {
    border-color: #00bfff;
    box-shadow: 0 0 10px rgba(0, 191, 255, 0.3);
    background: rgba(255, 255, 255, 0.25);
    outline: none;
}

.login-card .form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}
</style>

<?php require_once '../includes/footer.php'; ?>
