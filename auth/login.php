<?php
session_start();

require_once '../config/db_connection.php'; 

$errors = [];

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validations similar to register page
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{6,}$/', $password)) {
        $errors['password'] = 'Must include uppercase, lowercase, number, and special character.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set cookies for username and password for 7 days
                setcookie('username', $email, time() + (86400 * 7), "/");
                setcookie('password', $password, time() + (86400 * 7), "/");
                //set session
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

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      height: 100vh;
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #002853, #004f8c);
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
    }

    .login-card {
      width: 400px;
      background: rgba(0,0,0,0.25);
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }

    .login-card h2 {
      text-align: center;
      font-family: 'Pacifico', cursive;
      font-size: 36px;
      margin-bottom: 20px;
      color: #00bfff;
      text-shadow: 0 0 8px rgba(0,191,255,0.5);
    }

    .form-label {
      font-weight: 500;
      color: #e6e6e6;
    }

    .form-control {
      background: rgba(255,255,255,0.1);
      border: none;
      color: white;
    }

    .form-control:focus {
      box-shadow: 0 0 10px rgba(0,191,255,0.5);
    }

    .btn-primary {
      width: 100%;
      background: linear-gradient(90deg, #00bfff, #0072ff);
      border: none;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #0072ff, #00bfff);
    }

    .error-msg { color: #ff4d4f; margin-top: 6px; font-size: 0.875rem; }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Welcome Back</h2>
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
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$" title="At least 6 chars with uppercase, lowercase, number, and special character">
        <?php if (!empty($errors['password'])): ?>
          <div class="error-msg"><?= $errors['password'] ?></div>
        <?php endif; ?>
      </div>

      <div class="d-grid">
        <button type="submit" name="login" class="btn btn-primary">Login</button>
      </div>

      <div class="text-center mt-3">
        <small>Don't have an account? <a href="./register.php">Register here</a></small>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>