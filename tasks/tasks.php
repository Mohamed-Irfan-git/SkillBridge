<?php
session_start();
require_once "../config/db_connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Hub</title>

    <!-- Font Awesome -->
    <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
            integrity="sha512-pV8JfQHnLg8cuvIDpQBypSWB3yFlxK8FuP1KjILImk+dDcFYZCw1H5QxXHf1hkgzvV2gSe5FZqZP5L4vQvMzg=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
    />

    <style>
        body {
            background-color: #f7f9fb;
            font-family: 'Poppins', sans-serif;
            color: #1a202c;
            margin: 0;
            padding: 0;
        }

        .task-header {
            text-align: center;
            padding: 100px 20px 60px 20px;
        }

        .task-header h1 {
            font-weight: 700;
            font-size: 2.8rem;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .task-header span {
            color: #00b074;
        }

        .task-header p {
            font-size: 1.1rem;
            color: #718096;
            margin-top: 10px;
        }

        .task-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            padding: 40px 20px 100px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .task-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            width: 340px;
            padding: 40px 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .task-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 176, 116, 0.15);
            border-color: #00b074;
        }

        .task-card h2 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 15px;
        }

        .task-card p {
            font-size: 0.95rem;
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .task-card .btn {
            width: 100%;
            background: linear-gradient(90deg, #00b074, #00c896);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 176, 116, 0.3);
        }

        .task-card .btn:hover {
            background: linear-gradient(90deg, #00a76d, #00c896);
            box-shadow: 0 6px 15px rgba(0, 176, 116, 0.4);
        }

        .task-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            background: #e6f9f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #00b074;
            transition: all 0.3s ease;
        }

        .task-card:hover .task-icon {
            background: #00b074;
            color: #fff;
            transform: scale(1.1);
        }

        @media(max-width: 768px) {
            .task-card {
                width: 90%;
            }

            .task-header h1 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>

<body>
<div class="task-header">
    <h1>🚀 <span>Task Hub</span></h1>
    <p>Connect, collaborate, and grow — post your tasks or lend your expertise to others.</p>
</div>

<div class="task-container">
    <div class="task-card" onclick="location.href='post_task.php'">
        <div class="task-icon"><i class="fas fa-plus"></i></div>
        <h2>Create Task</h2>
        <p>Need help with a project? Post a detailed task and let skilled contributors assist you.</p>
        <button class="btn">Create a Task</button>
    </div>

    <div class="task-card" onclick="location.href='./view_task.php'">
        <div class="task-icon"><i class="fas fa-tasks"></i></div>
        <h2>View Tasks</h2>
        <p>Explore posted tasks and showcase your skills by solving real-world challenges.</p>
        <button class="btn">Browse Tasks</button>
    </div>
</div>
</body>
</html>
