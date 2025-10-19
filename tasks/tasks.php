<?php
session_start();
require_once "../includes/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Hub - SkillBridge</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      padding-top: 50px;
      font-family: "Poppins", sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      color: #fff;
    }

    .task-header {
      text-align: center;
      padding: 120px 20px 40px 20px;
    }

    .task-header h1 {
      font-family: 'Pacifico', cursive;
      font-size: 2.8rem;
      color: #00bfff;
      text-shadow: 0 0 10px rgba(0,191,255,0.5);
    }

    .task-header p {
      font-size: 1.2rem;
      color: #e0e0e0;
    }

    .task-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      padding: 40px 20px 80px 20px;
    }

    .task-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      width: 320px;
      padding: 40px 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      cursor: pointer;
      transition: 0.4s;
      text-align: center;
      color: #fff;
    }

    .task-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.5);
    }

    .task-card h2 {
      font-size: 1.8rem;
      margin-bottom: 15px;
      color: #00bfff;
      text-shadow: 0 0 8px rgba(0,191,255,0.5);
    }

    .task-card p {
      font-size: 1rem;
      color: #e0e0e0;
    }

    .task-card .btn {
      margin-top: 20px;
      width: 100%;
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      border: none;
      color: #fff;
      font-weight: 500;
      padding: 10px;
      border-radius: 8px;
      transition: 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .task-card .btn:hover {
      background: linear-gradient(90deg, #0072ff, #00c6ff);
      box-shadow: 0 0 15px rgba(0, 212, 255, 0.6);
    }

    /* Responsive */
    @media(max-width: 768px) {
      .task-card {
        width: 90%;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="task-header">
  <h1>Task Hub</h1>
  <p>Create tasks if you need help or view tasks to contribute your expertise.</p>
</div>

<!-- Task Cards -->
<div class="task-container">
  <!-- Create Task -->
  <div class="task-card" onclick="location.href='post_task.php'">
    <h2>Create Task</h2>
    <p>If you need help, post a task with details and wait for contributors to assist you.</p>
    <button class="btn">Go to Create Task</button>
  </div>

  <!-- View Tasks -->
  <div class="task-card" onclick="location.href='./view_task.php'">
    <h2>View Tasks</h2>
    <p>See all tasks posted by others and contribute by helping or completing them.</p>
    <button class="btn">Go to View Tasks</button>
  </div>
</div>

<!-- Optional Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once "../includes/footer.php";
?>
