<?php
session_start();
require_once '../config/db_connection.php';

$errors = [];
$success = '';

if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $success = 'Registration successful! Please login to continue.';
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                // Set cookies for login details (email and username, NOT password)
                setcookie('user_email', $user['email'], time() + (7 * 24 * 60 * 60), "/"); // 7 days
                setcookie('user_name', $user['name'], time() + (7 * 24 * 60 * 60), "/"); // 7 days
                setcookie('user_id', $user['user_id'], time() + (7 * 24 * 60 * 60), "/"); // 7 days
                setcookie('user_role', $user['role'] ?? 'student', time() + (7 * 24 * 60 * 60), "/");
                header("Location: ../view/dashboard.php");
                exit();
            } else {
                $errors['password'] = 'Invalid password. Please try again.';
            }
        } else {
            $errors['email'] = 'No account found with that email.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SkillBridge</title>
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
            min-height: 150vh;
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
            max-width: 460px;
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

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .error-feedback {
            color: #fca5a5;
            font-size: 0.8rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 102, 255, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.875rem;
            animation: slideDown 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-success i {
            color: #10b981;
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

        .forgot-password {
            text-align: right;
            margin-top: 8px;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--primary);
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

        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
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

        .features-list {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        .feature-item i {
            color: var(--secondary);
            font-size: 1rem;
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
                <i class="fas fa-bolt"></i>
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to continue your journey</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($errors['email']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                       placeholder="Enter your password"
                       required>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($errors['password']) ?>
                    </div>
                <?php endif; ?>
                <div class="forgot-password">
                    <a href="#">Forgot password?</a>
                </div>
            </div>

            <button type="submit" name="login" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>

        <div class="features-list">
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Access 10,000+ skill exchange opportunities</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Connect with professionals worldwide</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle"></i>
                <span>Earn points and unlock achievements</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>