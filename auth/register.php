<?php
global $conn;
session_start();
require_once '../config/db_connection.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];
    $bio = trim($_POST['bio']);
    $csrf_token = $_POST['csrf_token'];

    $errors = [];

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $errors[] = "Invalid request. Please try again.";
    }

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All required fields must be filled.";
    }

    if (!empty($username) && !preg_match('/^[A-Za-z0-9]{6,10}$/', $username)) {
        $errors[] = "Username must be 6-10 characters, letters and numbers only (no spaces).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (!empty($password) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/', $password)) {
        $errors[] = "Password must include uppercase, lowercase, number, and special character.";
    }

    if (!in_array($role, ['student', 'freelancer', 'admin'])) {
        $errors[] = "Invalid role selected.";
    }

    // Check duplicates
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR name = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Username or Email already registered!";
        }
        $stmt->close();
    }

    // Insert
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (name, email, password, role, bio) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $username, $email, $hashed_password, $role, $bio);

        if ($insert->execute()) {
            header("Location: login.php?register=success");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }

        $insert->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SkillBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0066FF;
            --primary-dark: #0052CC;
            --secondary: #00D9B1;
            --dark: #0A0E27;
            --gray-900: #1A1D3A;
            --gray-800: #2D3149;
            --gray-700: #4A5568;
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow-inline: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -300px;
            right: -200px;
            animation: pulse 8s ease-in-out infinite;
            filter: blur(80px);
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 217, 177, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            /*bottom: -250px;*/
            left: -150px;
            animation: pulse 10s ease-in-out infinite reverse;
            filter: blur(80px);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 500px;
        }

        .auth-box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 16px;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.4);
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        select.form-control {
            cursor: pointer;
        }

        select.form-control option {
            background: var(--gray-800);
            color: #fff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            pointer-events: none;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.4);
            margin-top: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: var(--secondary);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            padding: 0 16px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary);
            transform: translateX(-4px);
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 576px) {
            .auth-box {
                padding: 32px 24px;
            }

            .auth-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
<div class="auth-container">
    <a href="../index.php" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <div class="auth-box">
        <div class="brand">
            <div class="brand-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join the SkillBridge community today</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Choose a username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required pattern="^[A-Za-z0-9]{6,10}$"
                       minlength="6" maxlength="10">
                <div class="password-strength">6-10 characters, letters and numbers only</div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Create a strong password"
                       required
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$">
                <div class="password-strength">Must include uppercase, lowercase, number, and special character</div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control"
                       placeholder="Re-enter your password"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">I am a</label>
                <select name="role" class="form-control" required>
                    <option value="student" <?= (($_POST['role'] ?? '') === 'student') ? 'selected' : '' ?>>Student</option>
                    <option value="freelancer" <?= (($_POST['role'] ?? '') === 'freelancer') ? 'selected' : '' ?>>Freelancer</option>
                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Bio <span style="color: rgba(255,255,255,0.5);">(Optional)</span></label>
                <textarea name="bio" class="form-control"
                          placeholder="Tell us about yourself..."><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <button type="submit" name="register" class="btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>