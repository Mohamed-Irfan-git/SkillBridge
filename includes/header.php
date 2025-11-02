<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-pVnR8g0m+q/6B+vRSxYfF+p9ld9xh3kV0L/NZsE6R8+V3fJrYlP+GmVfVvEy2m8+g+RkVc6ZdL+ncFkkDg7W7Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <style>
        /* ---------------- Variables ---------------- */
        :root {
            --primary: #1f6feb;
            --primary-gradient: linear-gradient(90deg, #1f6feb, #60a5fa);
            --muted: #6b7280;
            --card-bg: #ffffff;
            --bg: #f3f6fb;
            --text-color: #111827;
            --nav-bg: rgba(255,255,255,0.9);
            --nav-link-color: #111827;
            --nav-link-hover: var(--primary);
            --notification-icon: #111827;
            --notification-badge-bg: #ff3b3b;
            --notification-badge-border: #ffffff;
        }

        body.dark {
            --bg: #071124;
            --card-bg: #0b1220;
            --primary: #3b82f6;
            --primary-gradient: linear-gradient(90deg, #3b82f6, #60a5fa);
            --muted: #9aa4b2;
            --text-color: #e5e7eb;
            --nav-bg: rgba(11,18,32,0.9);
            --nav-link-color: #d1d5db;
            --nav-link-hover: #3b82f6;
            --notification-icon: #e5e7eb;
            --notification-badge-bg: #ff3b3b;
            --notification-badge-border: var(--card-bg);
        }

        /* ---------------- Body ---------------- */
        body {
            background: var(--bg);
            color: var(--text-color);
            padding-top: 80px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        /* ---------------- Navbar ---------------- */
        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 1rem 1.5rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            border-radius: 0 0 20px 20px;
            transition: all 0.3s ease;
        }
        .navbar.scrolled { box-shadow: 0 6px 25px rgba(0,0,0,0.25); }

        .navbar-brand {
            font-weight: 800;
            font-size: 2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ---------------- Links ---------------- */
        .nav-link {
            color: var(--nav-link-color) !important;
            font-weight: 500;
            margin: 0 15px;
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link:hover,
        .nav-link.active { color: var(--nav-link-hover) !important; }
        .nav-link::after {
            content: "";
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--nav-link-hover);
            transition: width 0.3s;
        }
        .nav-link:hover::after,
        .nav-link.active::after { width: 100%; }

        /* ---------------- Notifications ---------------- */
        .notification-icon i { font-size: 1.4rem; color: var(--notification-icon); }
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -10px;
            background: var(--notification-badge-bg);
            color: #fff;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            border: 2px solid var(--notification-badge-border);
            box-shadow: 0 0 6px rgba(0,0,0,0.25);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255,59,59,0.4); }
            70% { box-shadow: 0 0 0 6px rgba(255,59,59,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,59,59,0); }
        }

        /* ---------------- Buttons ---------------- */
        .btn-desktop {
            background: var(--primary-gradient);
            color: #fff;
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 26px;
            border: none;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .btn-desktop:hover {
            background: linear-gradient(90deg, #60a5fa, #1f6feb);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        /* ---------------- Mobile Menu ---------------- */
        #mobileMenu {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            background: var(--nav-bg);
            backdrop-filter: blur(15px);
            padding-top: 80px;
            transition: 0.4s ease;
            z-index: 2000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            border-radius: 0 20px 20px 0;
        }
        .navbar .fa-bell {
            color: #fff;
        }
        .navbar.light-mode .fa-bell {
            color: #333;
        }


        #mobileMenu.show { left: 0; }
        #mobileMenu a {
            display: block;
            padding: 15px 25px;
            font-size: 1rem;
            color: var(--nav-link-color);
            text-decoration: none;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: 0.3s;
        }
        #mobileMenu a:hover,
        #mobileMenu a.active {
            background: rgba(0,0,0,0.05);
            border-left: 3px solid var(--primary);
            color: var(--nav-link-hover);
        }

        /* ---------------- Hamburger ---------------- */
        .menu-btn {
            border: none;
            background: transparent;
            color: var(--nav-link-color);
            font-size: 1.6rem;
        }

        @media (max-width: 992px) {
            .navbar { padding: 1rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="../index.php">SkillBridge</a>

        <!-- Mobile menu button -->
        <button class="menu-btn d-lg-none" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Desktop Menu -->
        <ul class="navbar-nav d-none d-lg-flex flex-row align-items-center mb-0">
            <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../tasks/tasks.php">Tasks</a></li>
            <li class="nav-item">
                <a class="nav-link" href="../reviews/add_review.php">Reviews</a></li>
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

<!-- Mobile Menu -->
<div id="mobileMenu">
    <a href="../index.php"><i class="fas fa-home me-2"></i>Home</a>
    <a href="../tasks/tasks.php"><i class="fas fa-tasks me-2"></i>Tasks</a>
    <a href="../view/notification.php"><i class="fa-solid fa-bell me-2"></i>Notifications</a>
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="../view/dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
        <a href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    <?php else: ?>
        <a href="../auth/login.php"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('show'));
    document.addEventListener('click', e => {
        if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
            mobileMenu.classList.remove('show');
        }
    });

    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
</script>
