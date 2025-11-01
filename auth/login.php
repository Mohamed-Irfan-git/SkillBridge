<?php
session_start();
require_once '../config/db_connection.php';

$errors = [];

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
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
    <title>SkillBridge Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e6f7ee, #b8f2d9);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 420px;
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            color: #333;
        }

        .login-box h3 {
            font-family: 'Pacifico', cursive;
            font-size: 32px;
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(90deg, #28a745, #198754);
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #198754, #28a745);
        }

        .error-msg { color: #ff4d4f; margin-top: 6px; font-size: 0.875rem; }

        a.text-info {
            color: #28a745;
            text-decoration: none;
        }

        a.text-info:hover {
            color: #198754;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-box { width: 90%; padding: 25px; }
        }
    </style>
</head>

<body>
<div class="login-box">
    <h3>SkillBridge Login</h3>
    <form action="" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="error-msg"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="error-msg"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <div class="d-grid">
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </div>

        <div class="text-center mt-3">
            <small>Don't have an account? <a href="./register.php" class="text-info">Register here</a></small>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
