<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch tasks
$sql = "SELECT t.*, u.name AS creator_name 
        FROM tasks t 
        JOIN users u ON t.user_id = u.user_id
        WHERE t.status='open'";

if ($search) {
    $sql .= " AND t.skill_required LIKE ?";
}

$stmt = $conn->prepare($sql);
if ($search) {
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
}
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
.card {
    background: #003f7d;
    border-radius: 12px;
}

.card-title {
    color: #00bfff;
    font-family: 'Poppins', sans-serif;
}

.card p {
    color: #e0e0e0;
}

.modal-content {
    background-color: #ece5dd;
    border-radius: 12px;
    overflow: hidden;
    color: #002853;
}

.chat-header {
    background-color: #075E54;
    color: white;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
}

.chat-box {
    background-color: #e5ddd5;
    height: 400px;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
}

.chat-message {
    max-width: 75%;
    margin-bottom: 10px;
    border-radius: 8px;
    padding: 8px 12px;
    position: relative;
    word-wrap: break-word;
    display: inline-block;
}

.sender {
    background-color: #DCF8C6;
    align-self: flex-end;
    text-align: right;
}

.receiver {
    background-color: #fff;
    align-self: flex-start;
}

.chat-message strong {
    display: block;
    font-size: 12px;
    color: #555;
    margin-bottom: 3px;
}

.chat-input {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #f0f0f0;
    border-top: 1px solid #ccc;
}

.chat-input textarea {
    flex: 1;
    border: none;
    border-radius: 20px;
    padding: 10px 15px;
    resize: none;
    height: 45px;
    outline: none;
    font-size: 14px;
}

.chat-input button {
    background-color: #075E54;
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    margin-left: 8px;
    cursor: pointer;
    font-size: 18px;
}

.chat-input button:hover {
    background-color: #0b7d6e;
}
</style>

<div class="container py-5">
    <h2 class="text-center mb-4">Available Tasks</h2>

    <!-- Search bar -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by skill..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary">Search</button>
        </div>
    </form>

    <div class="row">
        <?php if($tasks): ?>
            <?php foreach($tasks as $task): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm p-3">
                    <h3 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?>...</p>
                    <p><strong>Skill:</strong> <?php echo htmlspecialchars($task['skill_required']); ?></p>
                    <p><strong>Deadline:</strong> <?php echo htmlspecialchars($task['deadline']); ?></p>
                    <p><strong>Creator:</strong> <?php echo htmlspecialchars($task['creator_name']); ?></p>

                    <!-- View & Chat button -->
                    <button class="btn btn-success w-100 mt-2" data-bs-toggle="modal" data-bs-target="#chatModal<?php echo $task['task_id']; ?>">
                        💬 View & Chat
                    </button>

                    <!-- Apply button -->
                    <form method="POST" action="apply_task.php" class="mt-2">
                        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                        <button type="submit" class="btn btn-info w-100">Apply for Task</button>
                    </form>
                </div>
            </div>

            <!-- Chat Modal -->
            <div class="modal fade" id="chatModal<?php echo $task['task_id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="chat-header">💬 <?php echo htmlspecialchars($task['title']); ?></div>
                        <div id="chatBox<?php echo $task['task_id']; ?>" class="chat-box">Loading messages...</div>

                        <div class="chat-input">
                            <textarea id="chatInput<?php echo $task['task_id']; ?>" placeholder="Type a message..."></textarea>
                            <button onclick="sendMessage(<?php echo $task['task_id']; ?>)">➤</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No tasks found.</p>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_GET['applied']) && $_GET['applied'] == 1): ?>
    <script>
        alert("✅ You have successfully applied for the task!");
    </script>
<?php endif; ?>

<script>
// Load messages
function loadMessages(taskId) {
    fetch('load_messages.php?task_id=' + taskId)
        .then(res => res.text())
        .then(data => {
            const chatBox = document.getElementById('chatBox' + taskId);
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Send message
function sendMessage(taskId) {
    const input = document.getElementById('chatInput' + taskId);
    const message = input.value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('message', message);

    fetch('send_messages.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(() => {
            input.value = '';
            loadMessages(taskId);
        });
}

// Auto refresh chat
<?php foreach ($tasks as $task): ?>
setInterval(() => loadMessages(<?php echo $task['task_id']; ?>), 2000);
<?php endforeach; ?>
</script>

<?php require_once '../includes/footer.php'; ?>
