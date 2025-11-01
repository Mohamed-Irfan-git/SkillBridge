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
            $success = "✅ Task updated successfully!";
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $task = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "❌ Failed to update task. Try again.";
        }
        $updateStmt->close();
    }
}

$conn->close();
?>

<div class="container" style="max-width:650px; margin-top:100px; margin-bottom:60px;">
    <div class="task-card p-5">
        <h2 class="text-center mb-4">Edit Task</h2>

        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php elseif($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label class="form-label">Task Title</label>
                <input type="text" name="title" class="form-control" placeholder="Task Title" value="<?= htmlspecialchars($task['title']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Task Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Describe your task..." required><?= htmlspecialchars($task['description']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Skills Required</label>
                <input type="text" name="skill_required" class="form-control" placeholder="e.g., Web Development, Design" value="<?= htmlspecialchars($task['skill_required']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control" value="<?= $task['deadline']; ?>" required>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" name="update_task" class="btn btn-success flex-fill">Update Task</button>
                <a href="../view/dashboard.php" class="btn btn-outline-secondary flex-fill">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    /* Card Style */
    .task-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.25);
    }

    /* Header */
    .task-card h2 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #00b894; /* greenish */
        text-shadow: 0 0 5px rgba(0,184,148,0.3);
    }

    /* Form Controls */
    .task-card .form-control {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .task-card .form-control:focus {
        border-color: #00b894;
        box-shadow: 0 0 10px rgba(0,184,148,0.3);
        outline: none;
    }

    /* Labels */
    .task-card .form-label {
        font-weight: 500;
        font-size: 0.95rem;
        color: #333;
    }

    /* Buttons */
    .btn-success {
        background: linear-gradient(90deg, #00b894, #00d084);
        border: none;
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0,184,148,0.3);
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #00d084, #00b894);
        box-shadow: 0 8px 20px rgba(0,184,148,0.5);
    }

    .btn-outline-secondary {
        border-radius: 10px;
        padding: 12px;
        font-weight: 500;
    }
</style>

<?php require_once '../includes/footer.php'; ?>
