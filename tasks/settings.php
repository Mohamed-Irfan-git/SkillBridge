<?php
// settings.php
global $conn;
session_start();
require_once '../config/db_connection.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { font-family: 'Inter', sans-serif; background: #f3f6fb; transition: background 0.3s, color 0.3s; }
    .card-pro { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 6px 20px rgba(15,23,42,0.05); border: 1px solid rgba(15,23,42,0.03); transition: all 0.3s ease; }
    .card-pro:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(15,23,42,0.1); }
    .text-primary { color: #1f6feb !important; }
    .nav-tabs .nav-link { font-weight: 600; }
    body.dark { background: #0b1220; color: #e5e7eb; }
    body.dark .card-pro { background: #111b2c; border-color: rgba(255,255,255,0.1); }
</style>

<div class="container my-5">
    <h3 class="mb-4 font-bold text-2xl text-primary">⚙ Settings</h3>

    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#account">Account</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#security">Security</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#notifications">Notifications</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#privacy">Privacy</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#payment">Payment</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#theme">Theme</button></li>
    </ul>

    <div class="tab-content">

        <!-- Account Tab -->
        <div class="tab-pane fade show active" id="account">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Profile Info</h5>
                <form id="accountForm">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Security Tab -->
        <div class="tab-pane fade" id="security">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Change Password</h5>
                <form id="passwordForm">
                    <div class="mb-3"><label>Current Password</label><input type="password" class="form-control" name="current_password" required></div>
                    <div class="mb-3"><label>New Password</label><input type="password" class="form-control" name="new_password" required></div>
                    <div class="mb-3"><label>Confirm New Password</label><input type="password" class="form-control" name="confirm_password" required></div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div class="tab-pane fade" id="notifications">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Notification Preferences</h5>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                    <label class="form-check-label" for="emailNotif">Email Notifications</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="pushNotif" checked>
                    <label class="form-check-label" for="pushNotif">Push Notifications</label>
                </div>
            </div>
        </div>

        <!-- Privacy Tab -->
        <div class="tab-pane fade" id="privacy">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Privacy Settings</h5>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="profilePublic" checked>
                    <label class="form-check-label" for="profilePublic">Public Profile</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="searchVisibility" checked>
                    <label class="form-check-label" for="searchVisibility">Search Visibility</label>
                </div>
            </div>
        </div>

        <!-- Payment Tab -->
        <div class="tab-pane fade" id="payment">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Payment Info</h5>
                <form id="paymentForm">
                    <div class="mb-3">
                        <label>PayPal Email</label>
                        <input type="email" class="form-control" name="paypal_email" value="<?= htmlspecialchars($user['paypal_email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label>Bank Account</label>
                        <input type="text" class="form-control" name="bank_account" value="<?= htmlspecialchars($user['bank_account'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Payment Info</button>
                </form>
            </div>
        </div>

        <!-- Theme Tab -->
        <div class="tab-pane fade" id="theme">
            <div class="card-pro">
                <h5 class="mb-3 font-semibold">Appearance</h5>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="darkModeToggle" <?= isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="darkModeToggle">Enable Dark Mode</label>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function(){

        // Dark Mode toggle
        const darkToggle = $('#darkModeToggle');
        darkToggle.change(function(){
            $('body').toggleClass('dark', this.checked);
            $.post('update_dark_mode.php', { dark: this.checked });
        });

        // Update Profile AJAX
        $('#accountForm').submit(function(e){
            e.preventDefault();
            $.post('update_profile.php', $(this).serialize(), function(res){
                alert(res.message);
                if(res.success){
                    $('#accountForm input[name="name"]').val(res.updated.name);
                    $('#accountForm input[name="email"]').val(res.updated.email);
                    $('#accountForm input[name="username"]').val(res.updated.username);
                }
            }, 'json');
        });

        // Update Password AJAX
        $('#passwordForm').submit(function(e){
            e.preventDefault();
            $.post('update_password.php', $(this).serialize(), function(res){
                alert(res.message);
            }, 'json');
        });

        // Update Payment Info AJAX
        $('#paymentForm').submit(function(e){
            e.preventDefault();
            $.post('update_payment.php', $(this).serialize(), function(res){
                alert(res.message);
            }, 'json');
        });

    });
</script>

<?php require_once '../includes/footer.php'; ?>
