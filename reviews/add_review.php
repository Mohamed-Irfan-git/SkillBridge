<?php
session_start();
require_once '../config/db_connection.php';
require_once '../includes/header.php';

$success_message = '';
$error_message = '';

// Handle delete success/error messages
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = 'Review deleted successfully!';
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    $error_message = 'Error deleting review. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($rating === false || $rating < 1 || $rating > 5) {
        $error_message = 'Please select a valid rating between 1 and 5.';
    } elseif (empty($comment)) {
        $error_message = 'Please provide a comment.';
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, rating, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $rating, $comment);

        if ($stmt->execute()) {
            $success_message = 'Your review has been submitted successfully!';
        } else {
            $error_message = 'Error submitting review. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review - SkillBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .rating input {
            display: none;
        }
        .rating label {
            cursor: pointer;
            width: 40px;
            font-size: 30px;
            color: #ddd;
            padding: 5px;
        }
        .rating label:before {
            content: '★';
        }
        .rating input:checked ~ label {
            color: #ffd700;
        }
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffd700;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Add Your Review</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label">Rating</label>
                                <div class="rating mb-3">
                                    <input type="radio" name="rating" value="5" id="star5">
                                    <label for="star5"></label>
                                    <input type="radio" name="rating" value="4" id="star4">
                                    <label for="star4"></label>
                                    <input type="radio" name="rating" value="3" id="star3">
                                    <label for="star3"></label>
                                    <input type="radio" name="rating" value="2" id="star2">
                                    <label for="star2"></label>
                                    <input type="radio" name="rating" value="1" id="star1">
                                    <label for="star1"></label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label">Your Review</label>
                                <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="alert alert-info text-center">
                    <h4>Want to share your experience?</h4>
                    <p>Please <a href="../auth/login.php" class="alert-link">login</a> to add your review.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Reviews List Section -->
    <div class="container mb-5">
        <h2 class="text-center mb-4">
            All Reviews
            <?php if(!isset($_SESSION['user_id'])): ?>
            <div class="mt-2">
                <small class="text-muted">
                    <a href="../auth/login.php" class="text-decoration-none">Login</a> to add your review
                </small>
            </div>
            <?php endif; ?>
        </h2>
        <div class="row">
            <?php
            // Fetch all reviews with user names and user_id for deletion check
            $reviews_query = "SELECT r.*, u.name, u.user_id 
                            FROM reviews r 
                            JOIN users u ON r.user_id = u.user_id 
                            ORDER BY r.created_at DESC";
            $reviews_result = $conn->query($reviews_query);

            if ($reviews_result && $reviews_result->num_rows > 0) {
                while ($review = $reviews_result->fetch_assoc()) {
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title"><?php echo htmlspecialchars($review['name']); ?></h5>
                                    <div>
                                        <small class="text-muted me-3"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                        <?php if (isset($_SESSION['user_id']) && $review['user_id'] == $_SESSION['user_id']): ?>
                                            <form action="delete_review.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this review?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo '<i class="fas fa-star" style="color: ' . ($i <= $review['rating'] ? '#ffd700' : '#ddd') . '"></i>';
                                    }
                                    ?>
                                </div>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        No reviews yet. Be the first to add one!
                    </div>
                </div>
                <?php
            }
            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php require_once '../includes/footer.php'; ?>