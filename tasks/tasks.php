<?php
session_start();
require_once "../config/db_connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Hub - SkillBridge</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --secondary: #00D9B1;
            --dark: #0A0E27;
            --gray-900: #1A1D3A;
            --gray-800: #2D3149;
            --gray-100: #F7FAFC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--gray-900) 100%);
            min-height: 100vh;
            color: #fff;
            position: relative;
            overflow-inline: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -300px;
            right: -200px;
            filter: blur(80px);
            pointer-events: none;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        .task-header {
            text-align: center;
            padding: 120px 20px 80px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 102, 255, 0.1);
            border: 1px solid rgba(0, 102, 255, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 24px;
        }

        .task-header h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .task-header p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            padding: 0 24px 120px;
            max-width: 900px;
            margin: 0 auto;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            animation-fill-mode: backwards;
        }

        .task-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .task-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .task-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(0, 102, 255, 0.4);
            box-shadow: 0 24px 60px rgba(0, 102, 255, 0.2);
        }

        .task-card:hover::before {
            transform: scaleX(1);
        }

        .task-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 32px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.3);
        }

        .task-card:hover .task-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.4);
        }

        .task-card h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .task-card p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .task-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.3);
        }

        .task-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.4);
        }

        .task-btn i {
            font-size: 1.1rem;
        }

        .back-link {
            position: absolute;
            top: 40px;
            left: 40px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .back-link:hover {
            color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(0, 102, 255, 0.3);
            transform: translateX(-4px);
        }

        .stats-row {
            display: flex;
            justify-content: center;
            gap: 48px;
            margin-top: 48px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .task-header h1 {
                font-size: 2.5rem;
            }

            .task-header p {
                font-size: 1.05rem;
            }

            .task-grid {
                grid-template-columns: 1fr;
                padding: 0 20px 80px;
            }

            .task-card {
                padding: 40px 32px;
            }

            .back-link {
                top: 20px;
                left: 20px;
                padding: 10px 18px;
                font-size: 0.9rem;
            }

            .stats-row {
                gap: 32px;
            }

            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
<a href="../index.php" class="back-link">
    <i class="fas fa-arrow-left"></i>
    <span>Back</span>
</a>

<div class="container">
    <div class="task-header">
        <div class="header-badge">
            <i class="fas fa-bolt"></i>
            <span>Task Management</span>
        </div>

        <h1>
            <span class="gradient-text">Task Hub</span>
        </h1>

        <p>
            Connect with talented professionals, collaborate on projects, and grow your skills together.
        </p>

        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Active Tasks</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">2.5K</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">95%</div>
                <div class="stat-label">Success Rate</div>
            </div>
        </div>
    </div>

    <div class="task-grid">
        <div class="task-card" onclick="location.href='post_task.php'">
            <div class="task-icon">
                <i class="fas fa-plus"></i>
            </div>
            <h2>Create Task</h2>
            <p>Post a new task and connect with skilled contributors who can help bring your project to life.</p>
            <button class="task-btn">
                <i class="fas fa-rocket"></i>
                <span>Create Task</span>
            </button>
        </div>

        <div class="task-card" onclick="location.href='./view_task.php'">
            <div class="task-icon">
                <i class="fas fa-list-check"></i>
            </div>
            <h2>Browse Tasks</h2>
            <p>Explore available tasks and showcase your expertise by solving real-world challenges.</p>
            <button class="task-btn">
                <i class="fas fa-search"></i>
                <span>View Tasks</span>
            </button>
        </div>
    </div>
</div>
</body>
</html>