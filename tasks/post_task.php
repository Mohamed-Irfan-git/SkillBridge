<?php
global $conn;
session_start();
require_once '../includes/header.php';
require_once '../config/db_connection.php';

// Redirect if not logged in
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
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . '_' . basename($_FILES['task_photo']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['task_photo']['tmp_name'], $target_file)) {
            $photo_path = $file_name;
        } else {
            $error = "❌ Failed to upload photo. Check folder permissions.";
        }
    }

    // Insert into database
    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, skill_required, deadline, photo) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $error = "❌ Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("isssss", $user_id, $title, $description, $skill_required, $deadline, $photo_path);

            if ($stmt->execute()) {
                $success = "✅ Task created successfully!";
            } else {
                $error = "❌ Failed to create task: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<div class="container" style="margin-top:80px; max-width:800px; margin-bottom: 40px">
    <div class="task-card p-5">
        <h2 class="text-center mb-4">📌 Post a Task</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="form-label">Task Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., Build a WordPress Website" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Task Description</label>
                <textarea name="description" class="form-control" rows="6" placeholder="Provide detailed requirements..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Skills Required</label>
                <input type="text" name="skill_required" class="form-control" placeholder="e.g., Web Development, Photoshop" required>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Attach File (optional)</label>
                    <input type="file" name="task_photo" class="form-control" accept="image/*" onchange="previewImage(event)">
                    <div class="mt-2 text-center">
                        <img id="photoPreview" src="#" alt="Preview" style="max-width:180px; display:none; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.3);">
                    </div>
                </div>
            </div>

            <button type="submit" name="create_task" class="btn btn-gradient w-100">
                <i class="fas fa-paper-plane me-2"></i> Post Task
            </button>
        </form>
    </div>
</div>

<style>
    .task-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        padding: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .task-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    .task-card .form-control {
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        font-size: 1rem;
    }
    .btn-gradient {
        background: linear-gradient(90deg, #00c6ff, #0072ff);
        border: none;
        padding: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
        border-radius: 10px;
    }
    .btn-gradient:hover {
        background: linear-gradient(90deg, #0072ff, #00c6ff);
    }
</style>

<script>
    function previewImage(event){
        const input = event.target;
        const preview = document.getElementById('photoPreview');
        if(input.files && input.files[0]){
            const reader = new FileReader();
            reader.onload = function(e){
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>
