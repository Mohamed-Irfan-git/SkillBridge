<?php
session_start();
require_once '../includes/header.php';
require_once '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['create_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skill_required = trim($_POST['skill_required']);
    $deadline = $_POST['deadline'];
    $user_id = $_SESSION['user_id'];

    // Handle file upload
    $photo_path = NULL;
    if (!empty($_FILES['task_photo']['name'])) {
        $target_dir = "../uploads/tasks/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . '_' . basename($_FILES['task_photo']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['task_photo']['tmp_name'], $target_file)) {
            $photo_path = $file_name;
        } else {
            $error = "Failed to upload photo.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, skill_required, deadline) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $description, $skill_required, $deadline);

        if ($stmt->execute()) {
            $success = "Task created successfully!";
        } else {
            $error = "Failed to create task. Try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!-- Main Content -->
<div class="container" style="max-width:600px; margin-top:120px;">
    <div class="login-card p-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); border-radius: 15px; box-shadow:0 8px 20px rgba(0,0,0,0.3);">
        <h2 class="text-center mb-4" style="font-family:'Poppins', serif; color:#00bfff; text-shadow:0 0 10px rgba(0,191,255,0.5);">Create Task</h2>

        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php elseif($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label text-light">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Task Title" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Describe your task..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Skill Required</label>
                <input type="text" name="skill_required" class="form-control" placeholder="e.g., Web Development, Design" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Deadline</label>
                <input type="date" name="deadline" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Attach Photo (optional)</label>
                <input type="file" name="task_photo" class="form-control" accept="image/*">
            </div>

            <button type="submit" name="create_task" class="btn btn-primary w-100" style="background: linear-gradient(90deg, #00c6ff, #0072ff); border:none; padding:10px; font-weight:500; border-radius:8px; box-shadow:0 0 10px rgba(0,212,255,0.3); transition:0.3s;">Create Task</button>
        </form>
    </div>
</div>

<style>
/* Matching previous form style */
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
</style>

<?php require_once '../includes/footer.php'; ?>
