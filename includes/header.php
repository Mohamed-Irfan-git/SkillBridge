<?php
session_start();

// Example: logged-in user simulation
// $_SESSION['user_id'] = 1; 
$notification_count = 3; // Fetch dynamically from DB if needed
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
      min-height: 100vh;
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      color: #fff;
    }

    .navbar {
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .navbar-brand {
      font-family: 'Pacifico', cursive;
      color: #00bfff !important;
      font-size: 1.8rem;
    }

    .nav-link {
      color: #e0e0e0 !important;
      font-weight: 500;
      margin-right: 10px;
      transition: 0.3s;
      position: relative;
    }

    .nav-link:hover {
      color: #00bfff !important;
      text-shadow: 0 0 5px rgba(0,191,255,0.5);
    }

    .btn-outline-light {
      border-color: rgba(255,255,255,0.6);
      color: #fff;
      transition: 0.3s;
    }

    .btn-outline-light:hover {
      background: #00bfff;
      color: #000 !important;
      border-color: #00bfff;
      text-shadow: none;
    }

    .badge {
      position: absolute;
      top: -5px;
      right: -10px;
      padding: 3px 6px;
      font-size: 0.7rem;
      background: #ff3d3d;
      color: #fff;
      border-radius: 50%;
      box-shadow: 0 0 5px rgba(0,0,0,0.5);
    }

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
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="../index.php">SkillBridge</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="../index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../tasks/tasks.php">Tasks</a>
        </li>
        <li class="nav-item position-relative">
          <a class="nav-link" href="notifications.php">
            Notifications
            <?php if(isset($_SESSION['user_id']) && $notification_count > 0): ?>
              <span class="badge"><?php echo $notification_count; ?></span>
            <?php endif; ?>
          </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-sm btn-outline-light ms-2" href="../auth/logout.php">Logout</a>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="nav-link btn btn-sm btn-outline-light ms-2" href="../auth/login.php">Login</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
