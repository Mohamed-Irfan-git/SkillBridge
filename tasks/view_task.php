<?php
global $conn;
session_start();
require '../config/db_connection.php';
require '../includes/header.php';

$user_id = $_SESSION['user_id'] ?? null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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
    /* -------------------- Body & Fonts -------------------- */
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #0A0E27;
        color: #fff;
    }

    /* -------------------- Container -------------------- */
    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* -------------------- Header -------------------- */
    .task-list-header {
        text-align: center;
        padding: 100px 20px 50px;
    }

    .task-list-header h2 {
        font-size: 3rem;
        background: linear-gradient(90deg, #00b074, #00d4ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .task-list-header p {
        font-size: 1.15rem;
        color: #bbb;
        margin-top: 8px;
    }

    /* -------------------- Search -------------------- */
    .search-bar {
        max-width: 550px;
        margin: 0 auto 50px;
    }

    .input-group .form-control {
        border-radius: 14px 0 0 14px;
        border: 1px solid #444;
        background: rgba(255,255,255,0.05);
        color: #fff;
    }

    .input-group .form-control::placeholder {
        color: #aaa;
    }

    .input-group .btn-primary {
        border-radius: 0 14px 14px 0;
        background-color: #0072ff;
        border: none;
        font-weight: 600;
    }

    .input-group .btn-primary:hover {
        background-color: #00c6ff;
    }

    /* -------------------- Task Card -------------------- */
    .row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        padding-bottom: 80px;
    }

    .task-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .task-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
    }

    /* Task Icon */
    .task-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 20px;
        border-radius: 15px;
        background: linear-gradient(135deg, #00b074, #00d4ff);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 25px rgba(0, 176, 116, 0.3);
        transition: transform 0.3s;
    }

    .task-card:hover .task-icon {
        transform: scale(1.1) rotate(5deg);
    }

    /* Task Title & Meta */
    .task-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00b074;
        margin-bottom: 12px;
    }

    .task-card p, .task-card .meta {
        color: #ccc;
        margin-bottom: 6px;
    }

    /* -------------------- Buttons -------------------- */
    .btn-custom {
        width: 100%;
        font-weight: 600;
        border: none;
        padding: 12px;
        border-radius: 50px;
        margin-bottom: 6px;
        transition: all 0.3s ease;
    }

    .btn-success {
        background: linear-gradient(90deg, #00ff99, #00bfff);
        color: #fff;
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #00bfff, #00ff99);
        transform: translateY(-2px);
    }

    .btn-info {
        background: linear-gradient(90deg, #0072ff, #00c6ff);
        color: #fff;
    }

    .btn-info:hover {
        background: linear-gradient(90deg, #00c6ff, #0072ff);
        transform: translateY(-2px);
    }

    /* -------------------- Chat Modal -------------------- */
    /* -------------------- Chat Modal -------------------- */
    .modal-content {
        border-radius: 20px;
        overflow: visible;
        background: #f9f9f9; /* soft white */
        color: #333;
        padding: 0;
    }

    .chat-header {
        background: #e3e3e3; /* soft gray */
        color: #333;
        font-weight: 600;
        font-size: 1.2rem;
        padding: 16px 24px;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-box {
        background: #fff;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-message {
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 0.95rem;
        word-wrap: break-word;
    }

    .sender {
        background: #d1f0ff; /* light blue */
        color: #000;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }

    .receiver {
        background: #f0f0f0; /* light gray */
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }

    .chat-input {
        display: flex;
        gap: 10px;
        padding: 12px;
        background: #f9f9f9;
        border-top: 1px solid #ddd;
    }

    .chat-input textarea {
        flex: 1;
        border-radius: 30px;
        padding: 10px 15px;
        height: 45px;
        border: 1px solid #ccc;
        outline: none;
        background: #fff;
        color: #333;
        resize: none;
    }

    .chat-input button {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #b0d4ff; /* soft blue */
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: background 0.3s ease;
    }

    .chat-input button:hover {
        background: #80bfff; /* darker soft blue on hover */
    }

    /* Scrollbar for chat */
    .chat-box::-webkit-scrollbar {
        width: 6px;
    }

    .chat-box::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-box::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }


    /* -------------------- Responsive -------------------- */
    @media (max-width: 768px) {
        .task-list-header h2 { font-size: 2.2rem; }
        .task-card { padding: 25px 20px; }
        .chat-box { max-height: 300px; }
    }
</style>

<div class="container">
    <div class="task-list-header">
        <h2>💡 Available Tasks</h2>
        <p>Browse open tasks, connect with others, and showcase your expertise.</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="search-bar mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by skill..."
                   value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    <div class="row">
        <?php if($tasks): ?>
            <?php foreach($tasks as $task): ?>
                <div class="task-card">
                    <div class="task-icon"><i class="fas fa-briefcase"></i></div>
                    <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?>...</p>
                    <div class="meta">Skill: <?php echo htmlspecialchars($task['skill_required']); ?></div>
                    <div class="meta">Deadline: <?php echo htmlspecialchars($task['deadline']); ?></div>
                    <div class="meta">Creator: <?php echo htmlspecialchars($task['creator_name']); ?></div>

                    <div class="mt-3 d-flex flex-column gap-2">
                        <button class="btn btn-success btn-custom" data-bs-toggle="modal"
                                data-bs-target="#chatModal<?php echo $task['task_id']; ?>">
                            <i class="fas fa-comments"></i> View & Chat
                        </button>
                        <form method="POST" action="apply_task.php">
                            <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                            <button type="submit" class="btn btn-info btn-custom">
                                <i class="fas fa-paper-plane"></i> Apply for Task
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Chat Modal -->
                <div class="modal fade" id="chatModal<?php echo $task['task_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="chat-header d-flex align-items-center justify-content-between">
                                <div><i class="fas fa-comments me-2"></i><?php echo htmlspecialchars($task['title']); ?></div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div id="chatBox<?php echo $task['task_id']; ?>" class="chat-box">
                                <div class="text-center text-muted mt-5">Loading messages...</div>
                            </div>
                            <div class="chat-input">
                                <textarea id="chatInput<?php echo $task['task_id']; ?>" placeholder="Type a message..."></textarea>
                                <button onclick="sendMessage(<?php echo $task['task_id']; ?>)">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
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
    <script>alert("✅ You have successfully applied for the task!");</script>
<?php endif; ?>

<script>
    function loadMessages(taskId) {
        fetch('load_messages.php?task_id=' + taskId)
            .then(res => res.text())
            .then(data => {
                const chatBox = document.getElementById('chatBox' + taskId);
                chatBox.innerHTML = data;
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

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

    <?php foreach ($tasks as $task): ?>
    document.getElementById('chatModal<?php echo $task['task_id']; ?>').addEventListener('shown.bs.modal', function () {
        loadMessages(<?php echo $task['task_id']; ?>);
        window['chatInterval<?php echo $task['task_id']; ?>'] = setInterval(() => loadMessages(<?php echo $task['task_id']; ?>), 2000);
    });
    document.getElementById('chatModal<?php echo $task['task_id']; ?>').addEventListener('hidden.bs.modal', function () {
        clearInterval(window['chatInterval<?php echo $task['task_id']; ?>']);
    });
    <?php endforeach; ?>
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
