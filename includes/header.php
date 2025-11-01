<?php
session_start();
require_once __DIR__ . '/../config/db_connection.php';

// Notifications
$notification_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND status = 'unread'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $notification_count = $row['count'] ?? 0;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillBridge</title>

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0f1f6da.js" crossorigin="anonymous"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fb;
            padding-top: 90px; /* content below navbar */
        }

        /* NAVBAR */
        .navbar {
            background: #fff;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            padding: 1.1rem 1.5rem !important;
            min-height: 75px;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 2rem;
            color: #00b074 !important;
        }

        /* NAV LINKS */
        .nav-link {
            color: #333 !important;
            font-weight: 500;
            margin: 0 15px;
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link:hover { color: #00b074 !important; }
        .nav-link::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #00b074;
            transition: width 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        /* NOTIFICATIONS */
        .notification-icon { position: relative; }
        .notification-icon i {
            font-size: 1.4rem;
            color: #333;
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
            box-shadow: 0 0 6px rgba(0,0,0,0.25);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,59,59,0.4); }
            70% { box-shadow: 0 0 0 6px rgba(255,59,59,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,59,59,0); }
        }

        /* BUTTONS */
        .btn-desktop {
            background: #00b074;
            color: #fff;
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 26px;
            border: none;
            font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0,176,116,0.25);
            transition: all 0.3s ease;
        }
        .btn-desktop:hover {
            background: #009e68;
            box-shadow: 0 6px 18px rgba(0,176,116,0.35);
        }

        /* MOBILE MENU */
        #mobileMenu {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            background: #00b074;
            padding-top: 90px;
            transition: 0.4s ease;
            z-index: 2000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.3);
        }
        #mobileMenu.show { left: 0; }
        #mobileMenu a {
            display: block;
            padding: 15px 25px;
            font-size: 1rem;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: 0.3s;
        }
        #mobileMenu a:hover {
            background: rgba(255,255,255,0.1);
            border-left: 3px solid #fff;
        }

        /* HAMBURGER */
        .menu-btn {
            border: none;
            background: transparent;
            color: #333;
            font-size: 1.6rem;
        }

        @media (max-width: 992px) {
            .navbar { padding: 1rem; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="../index.php">SkillBridge</a>

        <!-- MOBILE MENU BUTTON -->
        <button class="menu-btn d-lg-none" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <!-- DESKTOP MENU -->
        <ul class="navbar-nav d-none d-lg-flex flex-row align-items-center mb-0">
            <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../tasks/tasks.php">Tasks</a></li>
            <li class="nav-item">
                <a class="nav-link notification-icon" href="../view/notification.php">
                    <i class="fas fa-bell"></i>
                    <?php if($notification_count > 0): ?>
                        <span class="notification-badge"><?php echo $notification_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="../view/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="btn btn-desktop ms-3" href="../auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="btn btn-desktop ms-3" href="../auth/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- MOBILE MENU -->
<div id="mobileMenu">
    <a href="../index.php"><i class="fas fa-home me-2"></i>Home</a>
    <a href="../tasks/tasks.php"><i class="fas fa-tasks me-2"></i>Tasks</a>
    <a href="../view/notification.php"><i class="fas fa-bell me-2"></i>Notifications</a>
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="../view/dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
        <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    <?php else: ?>
        <a href="../auth/login.php"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
    <?php endif; ?>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('show'));
    document.addEventListener('click', (e) => {
        if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
            mobileMenu.classList.remove('show');
        }
    });

    // Navbar shadow on scroll
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
</script>
