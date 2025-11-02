<?php
global $conn;
session_start();
require_once '../config/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';

// Basic validation
if (empty($name) || empty($email) || empty($username)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Update query
$stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=? WHERE user_id=?");
$stmt->bind_param("sssi", $name, $email, $username, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'updated' => ['name' => $name, 'email' => $email, 'username' => $username]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: '.$conn->error]);
}

$stmt->close();
?>
