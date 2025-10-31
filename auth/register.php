<?php
session_start();
require_once '../config/db_connection.php'; // database connection file

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Generate/Set OTP (always 1234 for testing)
if (empty($_SESSION['otp'])) {
    $_SESSION['otp'] = '1234';
}
$display_otp = $_SESSION['otp'];

if (isset($_POST['register'])) {
    // === 1. Collect input ===
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];
    $bio = trim($_POST['bio']);
    $csrf_token = $_POST['csrf_token'];
    $otp = trim($_POST['otp'] ?? '');

    $errors = [];

    // === 2. Validate ===
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $errors[] = "Invalid request. Please try again.";
    }

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All required fields must be filled.";
    }

    // Username must be 6-10 alphanumeric characters, no spaces
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

    // Password strength: at least one uppercase, one lowercase, one digit, one special char
    if (!empty($password) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/', $password)) {
        $errors[] = "Password must include uppercase, lowercase, number, and special character.";
    }

    if (!in_array($role, ['student', 'freelancer', 'admin'])) {
        $errors[] = "Invalid role selected.";
    }

    // OTP validation
    if (empty($otp)) {
        $errors[] = "OTP is required.";
    } elseif ($otp !== $_SESSION['otp']) {
        $errors[] = "Invalid OTP. Please enter the correct OTP.";
    }

    

    // === 4. Check duplicates ===
    if (empty($errors)) {
        // Check email
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email'] = "Email is already registered.";
        }
        $stmt->close();

        // Check username
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['username'] = "Username is already taken.";
        }
        $stmt->close();
    }

    // === 5. Insert if no errors ===
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conn->prepare("INSERT INTO users (name, email, password, role, bio) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $username, $email, $hashed_password, $role, $bio);

        if ($insert->execute()) {
            $_SESSION['user_id'] = $insert->insert_id;
            $_SESSION['role'] = $role;
            $success = "✅ Registration successful! You can now login.";
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
  <title>SkillBridge Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .register-card {
      width: 400px;
      background: rgba(0,0,0,0.25);
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }
    .register-card h3 {
      text-align: center;
      font-family: 'Pacifico', cursive;
      font-size: 36px;
      margin-bottom: 20px;
      color: #00bfff;
      text-shadow: 0 0 8px rgba(0,191,255,0.5);
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
    .error-msg { color: #ff4d4f; text-align: center; }
    .success-msg { color: #00ff99; text-align: center; }
    .otp-tools { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
    .otp-timer { color: #00bfff; font-size: 0.9rem; }
  </style>
</head>

<body>
  <div class="register-card">
    <h3 class="text-center mb-3">Create SkillBridge Account</h3>

    <?php 
      if (!empty($errors)) {
          foreach ($errors as $e) echo "<div class='error-msg'>$e</div>";
      }
      if (isset($success)) echo "<div class='success-msg'>$success</div>";
    ?>

    <form action="" method="post">
      <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required pattern="^[A-Za-z0-9]{6,10}$" minlength="6" maxlength="10" title="6-10 characters; letters and numbers only, no spaces">
        <?php if (!empty($errors['username'])): ?>
          <div class="error-msg"><?= $errors['username'] ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        <?php if (!empty($errors['email'])): ?>
          <div class="error-msg"><?= $errors['email'] ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$" title="At least 6 chars with uppercase, lowercase, number, and special character">
      </div>

      <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
          <option value="student">Student</option>
          <option value="freelancer">Freelancer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      

      <div class="mb-3">
        <label>Bio (optional)</label>
        <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label>OTP (Enter: <?= $display_otp ?>)</label>
        <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required maxlength="4" pattern="[0-9]{4}" title="Enter 4-digit OTP">
        <div class="otp-tools">
          <button type="button" id="sendOtpBtn" class="btn btn-outline-info btn-sm">Send OTP</button>
          <span id="otpTimer" class="otp-timer"></span>
        </div>
      </div>

      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <button type="submit" name="register" class="btn btn-primary mt-2">Register</button>

      <div class="text-center mt-3">
        <small>Already registered? <a href="login.php" class="text-info">Login here</a></small>
      </div>
    </form>
  </div>
  <script>
    (function() {
      const sendBtn = document.getElementById('sendOtpBtn');
      const timerEl = document.getElementById('otpTimer');
      let countdownInterval = null;

      function formatTime(seconds) {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        return m + ':' + s;
      }

      function startCountdown(duration) {
        let remaining = duration;
        timerEl.textContent = 'Time remaining: ' + formatTime(remaining);
        countdownInterval = setInterval(function() {
          remaining -= 1;
          if (remaining <= 0) {
            clearInterval(countdownInterval);
            countdownInterval = null;
            timerEl.textContent = '';
            sendBtn.disabled = false;
            sendBtn.textContent = 'Resend OTP';
            return;
          }
          timerEl.textContent = 'Time remaining: ' + formatTime(remaining);
        }, 1000);
      }

      if (sendBtn) {
        sendBtn.addEventListener('click', function() {
          // Fake send - just show a message and start timer
          sendBtn.disabled = true;
          sendBtn.textContent = 'OTP Sent';
          startCountdown(60); // 60 seconds cooldown
        });
      }
    })();
  </script>
</body>
</html>
