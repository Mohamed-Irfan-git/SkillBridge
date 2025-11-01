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
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #f7f9fb;
        color: #333;
    }

    /* Navbar (Dashboard Header Style) */
    .navbar {
        background: #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 0.9rem 1rem;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.8rem;
        color: #00b074 !important;
    }

    .nav-link {
        color: #333 !important;
        font-weight: 500;
        margin-right: 25px;
        position: relative;
        transition: 0.3s ease;
    }

    .nav-link:hover {
        color: #00b074 !important;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0%;
        height: 2px;
        background: #00b074;
        transition: 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* Notification Icon */
    .notification-icon {
        position: relative;
    }

    .notification-icon i {
        font-size: 1.3rem;
        color: #333;
        transition: color 0.3s;
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -10px;
        background: #ff3b3b;
        color: #fff;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 0.7rem;
        font-weight: 700;
        border: 2px solid #fff;
        box-shadow: 0 0 6px rgba(0,0,0,0.2);
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255,59,59,0.4); }
        70% { box-shadow: 0 0 0 6px rgba(255,59,59,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,59,59,0); }
    }

    /* Green Dashboard Button */
    .btn-desktop {
        background-color: #00b074 !important;
        color: #fff !important;
        font-weight: 600;
        border-radius: 25px;
        padding: 8px 22px;
        border: none !important;
        box-shadow: 0 4px 10px rgba(0,176,116,0.25);
        transition: all 0.3s ease;
    }

    .btn-desktop:hover {
        background-color: #009e68 !important;
        box-shadow: 0 6px 20px rgba(0,176,116,0.35);
        color: #fff !important;
    }

    /* Tasks Header */
    .task-list-header {
        text-align: center;
        padding: 100px 20px 40px;
    }

    .task-list-header h2 {
        font-size: 3rem;
        color: #00b074;
        text-shadow: 0 0 12px rgba(0,176,116,0.5);
    }

    .task-list-header p {
        font-size: 1.15rem;
        color: #555;
    }

    /* Search Bar */
    .search-bar {
        max-width: 550px;
        margin: 0 auto 50px;
    }

    .input-group .form-control {
        border-radius: 14px 0 0 14px;
        border: 1px solid #ccc;
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

    /* Task Cards */
    .task-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        padding: 30px;
        transition: all 0.3s ease;
        height: 100%;
    }

    .task-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.2);
    }

    .task-card h3 {
        color: #00b074;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .task-card p, .task-card .meta {
        color: #555;
    }

    /* Task Buttons */
    .btn-custom {
        width: 100%;
        font-weight: 600;
        border: none;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 6px;
    }

    .btn-success {
        background: linear-gradient(90deg, #00ff99, #00bfff);
        color: #fff;
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #00bfff, #00ff99);
    }

    .btn-info {
        background: linear-gradient(90deg, #0072ff, #00c6ff);
        color: #fff;
    }

    .btn-info:hover {
        background: linear-gradient(90deg, #00c6ff, #0072ff);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .task-list-header h2 { font-size: 2.2rem; }
        .task-card { padding: 25px 20px; }
    }

    .chat-message {
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 18px;
        font-size: 0.95rem;
        word-wrap: break-word;
    }

    .sender {
        background: linear-gradient(90deg, #00b074, #00d4ff);
        color: #fff;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }

    .receiver {
        background: #e0e0e0;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 0;
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
                <div class="col-md-6 mb-4">
                    <div class="task-card">
                        <h3><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($task['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?>...</p>
                        <div class="meta"><i class="fas fa-tools"></i> Skill: <?php echo htmlspecialchars($task['skill_required']); ?></div>
                        <div class="meta"><i class="fas fa-clock"></i> Deadline: <?php echo htmlspecialchars($task['deadline']); ?></div>
                        <div class="meta"><i class="fas fa-user"></i> Creator: <?php echo htmlspecialchars($task['creator_name']); ?></div>

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
                </div>

                <!-- Chat Modal -->
                <div class="modal fade" id="chatModal<?php echo $task['task_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content p-0" style="border-radius:20px; overflow:hidden;">
                            <!-- Chat Header -->
                            <div class="chat-header d-flex align-items-center justify-content-between px-4 py-3"
                                 style="background: linear-gradient(90deg, #00b074, #00d4ff); color:#fff; font-weight:600; font-size:1.2rem;">
                                <div>
                                    <i class="fas fa-comments me-2"></i>
                                    <?php echo htmlspecialchars($task['title']); ?>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Chat Body -->
                            <div id="chatBox<?php echo $task['task_id']; ?>"
                                 class="chat-box px-4 py-3"
                                 style="background:#f7f9fb; height:400px; overflow-y:auto; display:flex; flex-direction:column; gap:8px;">
                                <div class="text-center text-muted mt-5">Loading messages...</div>
                            </div>

                            <!-- Chat Input -->
                            <div class="chat-input d-flex align-items-center px-3 py-2" style="background:#e0e0e0; gap:8px;">
                <textarea id="chatInput<?php echo $task['task_id']; ?>"
                          placeholder="Type a message..."
                          style="flex:1; border:none; border-radius:20px; padding:10px 15px; height:42px; resize:none; outline:none;"></textarea>
                                <button class="btn"
                                        onclick="sendMessage(<?php echo $task['task_id']; ?>)"
                                        style="background: linear-gradient(90deg, #00b074, #00d4ff); color:#fff; width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
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
