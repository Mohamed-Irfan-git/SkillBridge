<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../config/db_connection.php';

if (isset($_POST['review_id'])) {
    $review_id = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    // Check if the review belongs to the user
    $check_query = "SELECT user_id FROM reviews WHERE review_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $review_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 1) {
        $review = $result->fetch_assoc();
        
        // Only allow deletion if the review belongs to the user
        if ($review['user_id'] === $user_id) {
            $delete_query = "DELETE FROM reviews WHERE review_id = ? AND user_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("ii", $review_id, $user_id);
            
            if ($delete_stmt->execute()) {
                header("Location: add_review.php?success=1");
                exit();
            }
            $delete_stmt->close();
        }
    }
    $check_stmt->close();
}

header("Location: add_review.php?error=1");
exit();
?>