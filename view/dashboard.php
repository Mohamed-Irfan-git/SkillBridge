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

// ---------- My Tasks (Owner) ----------
$myTasksSql = "SELECT t.*, 
                      (SELECT COUNT(*) FROM applications a WHERE a.task_id=t.task_id AND a.status='accepted') AS assigned_count,
                      (SELECT COUNT(*) FROM applications a WHERE a.task_id=t.task_id AND a.status='pending') AS pending_count
               FROM tasks t
               WHERE t.user_id=?
               ORDER BY t.created_at DESC
               LIMIT 50";
$stmt = $conn->prepare($myTasksSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myTasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Assigned Tasks (I am freelancer) ----------
$assignedSql = "SELECT t.*, u.name AS creator_name
                FROM tasks t
                JOIN applications a ON t.task_id=a.task_id
                JOIN users u ON t.user_id=u.user_id
                WHERE a.freelancer_id=? AND a.status='accepted'
                ORDER BY t.deadline ASC
                LIMIT 50";
$stmt2 = $conn->prepare($assignedSql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$assignedTasks = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

// ---------- Quick stats ----------
$statsSql = "SELECT
    (SELECT COUNT(*) FROM tasks WHERE user_id=?) AS total_created,
    (SELECT COUNT(*) FROM tasks WHERE status='open') AS total_open,
    (SELECT COUNT(*) FROM applications WHERE freelancer_id=?) AS total_applications,
    (SELECT COUNT(*) FROM applications WHERE freelancer_id=? AND status='accepted') AS total_hired";
$statsStmt = $conn->prepare($statsSql);
$statsStmt->bind_param("iii", $user_id, $user_id, $user_id);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$statsStmt->close();
?>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{
            --primary:#1f6feb;
            --muted:#6b7280;
            --card-bg: #ffffff;
            --bg: #f3f6fb;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: var(--bg);
            transition: all 0.3s ease;
        }

        .app-shell { display: grid; grid-template-columns: 260px 1fr; gap: 24px; min-height: 100vh; padding: 28px; }

        .sidebar {
            background: linear-gradient(180deg, rgba(31,111,235,0.06), rgba(255,255,255,0));
            border-radius: 14px;
            padding: 20px;
            height: calc(100vh - 56px);
            position: sticky;
            top: 28px;
            transition: all 0.3s ease;
        }
        .brand { display:flex; gap:12px; align-items:center; margin-bottom:18px; }
        .brand .logo { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--primary); color:#fff; font-weight:700; font-size:20px; box-shadow: 0 6px 18px rgba(31,111,235,0.12); }
        .brand .brand-name { font-weight:800; color:#0f172a; font-size:16px; transition: color 0.3s ease; }

        .nav-link {
            color: #374151;
            padding:10px 12px;
            border-radius:10px;
            display:flex;
            gap:10px;
            align-items:center;
            font-weight:600;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active { background: rgba(31,111,235,0.08); color: var(--primary); text-decoration:none; }

        .topbar { display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:18px; }
        .topbar h3 { margin:0; font-weight:800; color:#0f172a; transition: color 0.3s ease; }
        .search-input { width:420px; max-width:60%; }
        .top-controls { display:flex; gap:12px; align-items:center; }

        .card-pro {
            background:var(--card-bg);
            border-radius:14px;
            padding:18px;
            box-shadow: 0 6px 20px rgba(15,23,42,0.04);
            border: 1px solid rgba(15,23,42,0.03);
            transition: all 0.3s ease;
        }
        .stat-value { font-size:22px; font-weight:700; color:#111827; transition: color 0.3s ease; }
        .muted { color:var(--muted); font-weight:500; transition: color 0.3s ease; }

        .task-row { display:flex; gap:12px; align-items:flex-start; padding:14px; border-radius:12px; transition:all .18s ease; }
        .task-row:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(15,23,42,0.06); }

        .badge-open { background:#e6f0ff; color:#0f62fe; font-weight:600; padding:6px 10px; border-radius:999px; }
        .badge-assigned { background:#fff7ed; color:#b45309; font-weight:600; padding:6px 10px; border-radius:999px; }
        .badge-complete { background:#ecfdf5; color:#059669; font-weight:600; padding:6px 10px; border-radius:999px; }

        .avatar-stack { display:flex; gap:-8px; }
        .avatar { width:36px; height:36px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; color:#fff; font-weight:700; border: 2px solid #fff; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }

        @media (max-width: 980px) {
            .app-shell { grid-template-columns: 1fr; padding: 18px; }
            .sidebar { position:relative; height:auto; display:flex; gap:12px; overflow:auto; }
            .search-input { width: 100%; max-width: unset; }
        }

        body.dark {
            --card-bg: #0b1220;
            --bg: #071124;
            --muted: #9aa4b2;
            --primary: #3b82f6;
            color: #e5e7eb;
        }

        body.dark .card-pro {
            background: var(--card-bg);
            border-color: rgba(255,255,255,0.1);
        }

        body.dark .sidebar {
            background: linear-gradient(180deg, rgba(31,111,235,0.1), rgba(255,255,255,0.02));
        }

        body.dark .brand-name,
        body.dark .topbar h3 {
            color: #f9fafb !important;
        }

        body.dark .nav-link {
            color: #d1d5db;
        }

        body.dark .nav-link:hover,
        body.dark .nav-link.active {
            background: rgba(59,130,246,0.15);
            color: #3b82f6;
        }

        body.dark .stat-value {
            color: #f9fafb;
        }

        body.dark .form-control,
        body.dark .input-group-text {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        body.dark .btn-outline-secondary {
            border-color: #6b7280;
            color: #9ca3af;
        }

        body.dark .btn-outline-secondary:hover {
            background: #374151;
            color: #f9fafb;
        }

        body.dark .list-group-item {
            background: var(--card-bg);
            border-color: rgba(255,255,255,0.1);
            color: #e5e7eb;
        }

        /* Debug console styles */
        #debugConsole {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 400px;
            max-height: 300px;
            background: #1e293b;
            color: #e2e8f0;
            padding: 12px;
            border-radius: 8px 0 0 0;
            font-family: monospace;
            font-size: 12px;
            overflow-y: auto;
            z-index: 10000;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
        }
        #debugConsole .log { padding: 4px 0; border-bottom: 1px solid #334155; }
        #debugConsole .log.error { color: #ef4444; }
        #debugConsole .log.success { color: #10b981; }
        #debugConsole .log.info { color: #3b82f6; }
    </style>

    <div class="app-shell" style="margin-top: 50px">

        <aside class="sidebar">
            <div class="brand">
                <div class="logo"><?= strtoupper(substr($userProfile['name'],0,1)) ?></div>
                <div>
                    <div class="brand-name"><?= htmlspecialchars($userProfile['name']) ?></div>
                    <div class="muted" style="font-size:13px;"><?= htmlspecialchars($userProfile['role'] ?? 'Freelancer') ?></div>
                </div>
            </div>

            <nav class="mt-3">
                <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="../tasks/my_task.php" class="nav-link"><i class="bi bi-briefcase"></i> My Tasks</a>
                <a href="../tasks/assign_task.php" class="nav-link"><i class="bi bi-people"></i> Assigned Tasks</a>
                <a href="#" class="nav-link"><i class="bi bi-gear"></i> Settings</a>
                <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>

            <hr class="my-4">

            <div class="card-pro">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div class="muted">Total Created</div>
                        <div class="stat-value"><?= (int)$stats['total_created'] ?></div>
                    </div>
                    <div>
                        <div class="muted">Hired</div>
                        <div class="stat-value"><?= (int)$stats['total_hired'] ?></div>
                    </div>
                </div>
            </div>

            <div style="height:18px"></div>

            <div class="card-pro text-center">
                <div style="font-weight:700;">Create New Task</div>
                <p class="muted" style="font-size:13px;">Post a new job and find freelancers quickly</p>
                <a href="../tasks/post_task.php" class="btn btn-primary w-100 rounded-md">+ New Task</a>
            </div>
        </aside>

        <main>
            <div class="topbar mb-4">
                <div class="d-flex align-items-center gap-3">
                    <h3>Dashboard</h3>
                    <div class="muted" style="font-size:13px;">Welcome back — manage your gigs and deadlines</div>
                </div>

                <div class="top-controls">
                    <div class="input-group search-input">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input id="globalSearch" type="text" class="form-control" placeholder="Search tasks, freelancers, skills...">
                    </div>

                    <button id="themeToggle" class="btn btn-outline-secondary" title="Toggle theme"><i class="bi bi-moon-stars"></i></button>

                    <div class="position-relative">
                        <button class="btn btn-outline-primary rounded-circle p-2"><i class="bi bi-bell"></i></button>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($userProfile['name']) ?>&background=0D6EFD&color=fff&rounded=true"
                             alt="avatar" style="width:44px; height:44px; border-radius:10px;">
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card-pro">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="muted">Open Tasks</div>
                                <div class="stat-value"><?= (int)$stats['total_open'] ?></div>
                            </div>
                            <div class="text-end">
                                <div class="muted">Applications</div>
                                <div class="stat-value"><?= (int)$stats['total_applications'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-pro">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="muted">Your Earnings</div>
                                <div class="stat-value">LKR 0.00</div>
                            </div>
                            <div class="text-end">
                                <div class="muted">Active Contracts</div>
                                <div class="stat-value">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-pro">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="muted">Quick Actions</div>
                                <div style="margin-top:6px;">
                                    <a href="../tasks/post_task.php" class="btn btn-sm btn-primary me-2">Post Task</a>
                                    <a href="../tasks/view_task.php" class="btn btn-sm btn-outline-secondary">Browse</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-pro mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 style="margin:0; font-weight:800;">My Tasks</h5>
                                <div class="muted" style="font-size:13px;">Tasks you've posted</div>
                            </div>
                            <div>
                                <a href="../tasks/post_task.php" class="btn btn-primary">+ Post New Task</a>
                            </div>
                        </div>

                        <?php if ($myTasks): ?>
                            <div class="list-group">
                                <?php foreach ($myTasks as $task): ?>
                                    <div class="list-group-item mb-3 task-row card-pro" data-task-id="<?= $task['task_id'] ?>">
                                        <div style="flex:1;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div style="font-weight:700; font-size:15px;"><?= htmlspecialchars($task['title']) ?></div>
                                                    <div class="muted" style="font-size:13px;"><?= htmlspecialchars(substr($task['description'],0,120)) ?>...</div>
                                                    <div class="mt-2 d-flex gap-2 align-items-center">
                                                        <span class="badge badge-open"><?= ucfirst($task['status']) ?></span>
                                                        <span class="muted" style="font-size:13px;"><i class="bi bi-gear"></i> <?= htmlspecialchars($task['skill_required']) ?></span>
                                                    </div>
                                                </div>

                                                <div style="min-width:160px; text-align:right;">
                                                    <div class="muted" style="font-size:13px;">Task ID: <?= $task['task_id'] ?></div>
                                                    <div class="mt-2 d-flex justify-content-end gap-2">
                                                        <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-chat-dots"></i></a>
                                                        <a href="../tasks/edit_task.php?task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                                        <button data-task-id="<?= $task['task_id'] ?>" class="btn btn-sm btn-danger delete-task-btn"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-6 muted">You haven't posted any tasks yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-pro mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 style="margin:0; font-weight:800;">Assigned to You</h6>
                                <div class="muted" style="font-size:13px;">Tasks you're working on</div>
                            </div>
                            <div class="muted" style="font-size:13px;">Active: <?= count($assignedTasks) ?></div>
                        </div>

                        <?php if ($assignedTasks): ?>
                            <div class="list-group">
                                <?php foreach ($assignedTasks as $task): ?>
                                    <div class="list-group-item mb-2 card-pro">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div style="font-weight:700;"><?= htmlspecialchars($task['title']) ?></div>
                                                <div class="muted" style="font-size:13px;"><?= htmlspecialchars(substr($task['description'],0,90)) ?>...</div>
                                                <div class="mt-2">
                                                    <span class="muted" style="font-size:12px;"><i class="bi bi-person"></i> <?= htmlspecialchars($task['creator_name']) ?></span>
                                                    <span class="muted ms-2" style="font-size:12px;"><i class="bi bi-calendar"></i> <?= htmlspecialchars(date('M d, Y', strtotime($task['deadline'] ?? $task['created_at']))) ?></span>
                                                </div>
                                            </div>
                                            <div style="min-width:120px; text-align:right;">
                                                <div class="badge <?= $task['status']=='open' ? 'badge-open' : ($task['status']=='assigned' ? 'badge-assigned' : 'badge-complete') ?>">
                                                    <?= ucfirst($task['status']) ?>
                                                </div>
                                                <a href="../tasks/task_chat.php?task_id=<?= $task['task_id'] ?>" class="btn btn-sm btn-primary mt-2 w-100">Open</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-6 muted">You're not assigned to any tasks yet.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card-pro">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 style="margin:0; font-weight:800;">Recent Activity</h6>
                                <div class="muted" style="font-size:13px;">Notifications & updates</div>
                            </div>
                            <a href="#" class="muted" style="font-size:13px;">See all</a>
                        </div>

                        <ul class="list-unstyled">
                            <li class="d-flex gap-3 mb-3">
                                <div class="avatar" style="background:#ef4444;">A</div>
                                <div>
                                    <div style="font-weight:700;">New application received</div>
                                    <div class="muted" style="font-size:13px;">You received an application for "Frontend Developer".</div>
                                    <div class="muted" style="font-size:12px;">2 hours ago</div>
                                </div>
                            </li>

                            <li class="d-flex gap-3 mb-3">
                                <div class="avatar" style="background:#0ea5a4;">M</div>
                                <div>
                                    <div style="font-weight:700;">Milestone completed</div>
                                    <div class="muted" style="font-size:13px;">Freelancer finished the 1st milestone for "Logo Design".</div>
                                    <div class="muted" style="font-size:12px;">1 day ago</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

<!--    <!-- DEBUG CONSOLE -->-->
<!--    <div id="debugConsole">-->
<!--        <div style="font-weight:bold; margin-bottom:8px; border-bottom:2px solid #3b82f6; padding-bottom:4px;">🔍 DEBUG CONSOLE</div>-->
<!--        <div id="debugLogs"></div>-->
<!--    </div>-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug logger
        function debugLog(message, type = 'info') {
            const logs = document.getElementById('debugLogs');
            const log = document.createElement('div');
            log.className = `log ${type}`;
            log.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logs.appendChild(log);
            logs.scrollTop = logs.scrollHeight;
            console.log(message);
        }

        debugLog('Dashboard loaded', 'success');
        debugLog('Current URL: ' + window.location.href, 'info');

        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark');
            themeToggle.innerHTML = '<i class="bi bi-sun"></i>';
        }

        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark');
            if (document.body.classList.contains('dark')) {
                themeToggle.innerHTML = '<i class="bi bi-sun"></i>';
                localStorage.setItem('theme', 'dark');
            } else {
                themeToggle.innerHTML = '<i class="bi bi-moon-stars"></i>';
                localStorage.setItem('theme', 'light');
            }
        });

        // Delete task
        const deleteButtons = document.querySelectorAll('.delete-task-btn');
        debugLog(`Found ${deleteButtons.length} delete buttons`, 'info');

        deleteButtons.forEach((btn, index) => {
            const taskId = btn.getAttribute('data-task-id');
            debugLog(`Button ${index + 1}: Task ID = ${taskId}`, 'info');

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const id = this.getAttribute('data-task-id');
                debugLog(`Delete clicked for Task ID: ${id}`, 'info');

                if (!confirm(`Delete task #${id}? This cannot be undone.`)) {
                    debugLog('User cancelled deletion', 'info');
                    return;
                }

                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                this.disabled = true;

                // Check current folder structure
                const currentPath = window.location.pathname;
                const folderMatch = currentPath.match(/(.+\/)[^\/]+$/);
                const deleteUrl = folderMatch ? folderMatch[1] + '../tasks/delete_task.php' : '../tasks/delete_task.php';

                debugLog(`Sending POST to: ${deleteUrl}`, 'info');
                debugLog(`POST data: task_id=${id}`, 'info');

                fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'task_id=' + encodeURIComponent(id)
                })
                    .then(response => {
                        debugLog(`Response status: ${response.status}`, response.ok ? 'success' : 'error');
                        return response.text();
                    })
                    .then(data => {
                        debugLog(`Response data: "${data}"`, 'info');

                        const trimmedData = data.trim();
                        if (trimmedData === 'success') {
                            debugLog('Delete successful!', 'success');

                            const taskRow = this.closest('.task-row');
                            if (taskRow) {
                                taskRow.style.transition = 'opacity 0.3s ease';
                                taskRow.style.opacity = '0';

                                setTimeout(() => {
                                    taskRow.remove();
                                    debugLog('Task row removed from DOM', 'success');

                                    if (document.querySelectorAll('.task-row').length === 0) {
                                        debugLog('No more tasks, reloading...', 'info');
                                        location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            debugLog(`Delete failed: ${trimmedData}`, 'error');
                            alert('Error: ' + trimmedData);
                            this.innerHTML = originalHTML;
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        debugLog(`Fetch error: ${error.message}`, 'error');
                        alert('Network error: ' + error.message);
                        this.innerHTML = originalHTML;
                        this.disabled = false;
                    });
            });
        });

        // Global search
        document.getElementById('globalSearch').addEventListener('input', function(){
            const q = this.value.toLowerCase();
            document.querySelectorAll('.task-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    </script>

<?php require_once '../includes/footer.php'; ?>