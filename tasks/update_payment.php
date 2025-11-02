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
$paypal_email = $_POST['paypal_email'] ?? '';
$bank_account = $_POST['bank_account'] ?? '';

// Optional: basic validation
if (!filter_var($paypal_email, FILTER_VALIDATE_EMAIL) && !empty($paypal_email)) {
    echo json_encode(['success' => false, 'message' => 'Invalid PayPal email']);
    exit();
}

$stmt = $conn->prepare("UPDATE users SET paypal_email=?, bank_account=? WHERE user_id=?");
$stmt->bind_param("ssi", $paypal_email, $bank_account, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment info updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating payment info: '.$conn->error]);
}

$stmt->close();
?>
