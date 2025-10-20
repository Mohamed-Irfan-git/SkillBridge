<?php
global $conn;
session_start();

require_once __DIR__ . '/../config/db_connection.php';

$notification_count = 0;
    if (isset($_SESSION['user_id'])) {

        $user_id = $_SESSION['user_id'];
        $sql = "SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND status = 'unread'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $notification_count = $row['count'] ?? 0;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkillBridge</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      color: #fff;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .navbar-brand {
      font-family: 'Paoppins', cursive;
      color: #00bfff !important;
      font-size: 1.8rem;
    }

    .nav-link {
      color: #e0e0e0 !important;
      font-weight: 500;
      margin-right: 40px;
      position: relative;
      transition: 0.3s;
    }

    .nav-link:hover {
      color: #00bfff !important;
      text-shadow: 0 0 5px rgba(0,191,255,0.5);
    }

    /* Notification badge */
    .badge {
      padding: 3px 6px;
      font-size: 0.7rem;
      background: #ff3d3d;
      color: #fff;
      border-radius: 50%;
      box-shadow: 0 0 5px rgba(0,0,0,0.5);
      display: inline-block;
    }

    /* Buttons for desktop login/logout */
    .btn-desktop {
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      color: #fff !important;
      font-weight: 500;
      border-radius: 25px;
      padding: 6px 18px;
      border: none;
      transition: 0.3s;
      box-shadow: 0 4px 10px rgba(0,191,255,0.4);
    }

    .btn-desktop:hover {
      background: linear-gradient(90deg, #0072ff, #00c6ff);
      color: #fff !important;
      box-shadow: 0 6px 15px rgba(0,191,255,0.6);
    }

    /* Mobile Side Menu */
    #mobileMenu {
      position: fixed;
      top: 0;
      left: -250px;
      width: 250px;
      height: 100%;
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      z-index: 1050;
      padding-top: 60px;
      transition: 0.3s;
    }

    #mobileMenu a {
      display: block;
      padding: 15px 20px;
      color: #e0e0e0;
      text-decoration: none;
      font-weight: 500;
    }

    #mobileMenu a:hover {
      background: #004a99;
      color: #00bfff;
    }

    #mobileMenu .badge {
      position: absolute;
      top: 12px;
      right: 20px;
    }

    #mobileMenu.show {
      left: 0;
    }

    /* Content */
    .content {
      padding: 100px 40px 40px 40px;
      text-align: center;
      color: #fff;
    }

    .content h1 {
      font-size: 3rem;
      text-shadow: 0 0 10px rgba(0,191,255,0.5);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark fixed-top">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="../index.php">SkillBridge</a>
    <button class="btn d-lg-none" id="menuBtn">
      <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav d-none d-lg-flex flex-row align-items-center">
      <li class="nav-item">
        <a class="nav-link" href="../index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../tasks/tasks.php">Tasks</a>
      </li>
      <li class="nav-item position-relative">
        <a class="nav-link" href="../view/notification.php">
          Notifications
          <?php if(isset($_SESSION['user_id']) && $notification_count > 0): ?>
            <span class="badge position-absolute top-0 start-100 translate-middle">
              <?php echo $notification_count; ?>
            </span>
          <?php endif; ?>
        </a>
      </li>
      <?php if(isset($_SESSION['user_id'])): ?>
      <li class="nav-item">
        <a class="nav-link" href="../view/dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="btn btn-desktop ms-3" href="../auth/logout.php">Logout</a>
      </li>
      <?php else: ?>
      <li class="nav-item">
        <a class="btn btn-desktop ms-3" href="../auth/login.php">Login</a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- Mobile Side Menu -->
<div id="mobileMenu">
  <a href="../index.php">Home</a>
  <a href="../tasks/tasks.php">Tasks</a>
  <a href="../view/notification.php">
    Notifications
    <?php if(isset($_SESSION['user_id']) && $notification_count > 0): ?>
      <span class="badge"><?php echo $notification_count; ?></span>
    <?php endif; ?>
  </a>
  <?php if(isset($_SESSION['user_id'])): ?>
  <a href="../view/dashboard.php">Dashboard</a>
  <a href="../auth/logout.php">Logout</a>
  <?php else: ?>
  <a href="../auth/login.php">Login</a>
  <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const menuBtn = document.getElementById('menuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('show');
  });

  // Close mobile menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
      mobileMenu.classList.remove('show');
    }
  });
</script>
</body>
</html>
