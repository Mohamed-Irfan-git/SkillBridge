<?php
session_start();

require_once '../config/db_connection.php'; 

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

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
            echo "<script>alert('Invalid password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }

    $stmt->close();
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
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #002853, #003f7d, #0059a0);
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      background: rgba(0,0,0,0.25);
      backdrop-filter: blur(12px);
      border-radius: 15px;
      padding: 40px 30px;
      box-shadow: 0 0 25px rgba(0,0,0,0.3);
      animation: fadeIn 1s ease-in-out;
    }

    .login-card h2 {
      text-align: center;
      font-family: 'Pacifico', cursive;
      font-size: 36px;
      margin-bottom: 25px;
      color: #00bfff;
      text-shadow: 0 0 8px rgba(0,191,255,0.5);
    }

    .form-label {
      font-weight: 500;
      color: #e6e6e6;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      border-radius: 8px;
      padding: 10px;
      transition: 0.3s;
    }

    .form-control:focus {
      border-color: #00bfff;
      box-shadow: 0 0 10px rgba(0,191,255,0.3);
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }

    .btn-primary {
      width: 100%;
      border: none;
      background: linear-gradient(90deg, #00bfff, #0072ff);
      color: white;
      font-weight: 500;
      padding: 10px;
      border-radius: 8px;
      transition: 0.3s ease;
      box-shadow: 0 0 10px rgba(0,191,255,0.3);
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #0072ff, #00bfff);
      box-shadow: 0 0 15px rgba(0,191,255,0.6);
    }

    .text-center a {
      color: #00bfff;
      text-decoration: none;
      font-weight: 500;
    }

    .text-center a:hover {
      text-decoration: underline;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Welcome Back</h2>
    <p class="text-center text-white-50 mb-4">Exchange your skills, manage tasks, and track notifications easily.</p>
    <form action="" method="post">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
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
