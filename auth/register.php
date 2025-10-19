<?php
session_start();
require_once '../config/db_connection.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $email, $hashed_password, $role);
        if ($insert->execute()) {
            $_SESSION['user_id'] = $insert->insert_id;
            $_SESSION['role'] = $role;
            header("Location: ./login.php");
            exit();
        } else {
            $error = "Registration failed. Try again.";
        }
        $insert->close();
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
  <title>SkillBridge Register</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
      margin-bottom: 20px;
      color: #00bfff;
      text-shadow: 0 0 8px rgba(0,191,255,0.5);
    }

    .login-card p {
      text-align: center;
      margin-bottom: 25px;
      color: #e6e6e6;
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

    .error-msg {
      color: #ff4d4f;
      margin-bottom: 10px;
      text-align: center;
    }

    .dark-select {
      background: rgba(255, 255, 255, 0.05);
      color: #e6e6e6;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      padding: 10px;
      font-weight: 500;
      transition: 0.3s;
      -webkit-appearance: none;
      appearance: none;
      background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white"><polygon points="0,0 20,0 10,10"/></svg>');
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 12px;
    }

    .dark-select:focus {
      border-color: #00bfff;
      box-shadow: 0 0 10px rgba(0,191,255,0.3);
      background: rgba(255, 255, 255, 0.08);
      outline: none;
    }

    .dark-select option {
      background: #203a43;
      color: #e6e6e6;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body>
  <div class="login-card">
    <h2>Join SkillBridge</h2>
    <p>Exchange your skills, manage tasks, and connect with others seamlessly.</p>

    <?php if (isset($error)) echo '<div class="error-msg">' . $error . '</div>'; ?>

    <form action="" method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Your Name" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select name="role" id="role" class="form-control dark-select" required>
          <option value="student">Student</option>
          <option value="freelancer">Freelancer</option>
          <option value="admin" disabled>Admin</option>
        </select>
      </div>

      <div class="d-grid">
        <button type="submit" name="register" class="btn btn-primary">Register</button>
      </div>

      <div class="text-center mt-3">
        <small>Already have an account? <a href="login.php">Login here</a></small>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
