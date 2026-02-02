<?php
require_once 'includes/config.php';

if(!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: properties.php");
    exit();
}

$property_id = (int)$_GET['id'];
$buyer_id = $_SESSION['buyer_id'];

// Fetch property details
$query = "SELECT * FROM properties WHERE property_id = $property_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: properties.php");
    exit();
}

$property = $result->fetch_assoc();

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $comment = $conn->real_escape_string(trim($_POST['comment']));
    
    if($rating < 1 || $rating > 5) {
        $error = "Please select a rating";
    } elseif(empty($comment)) {
        $error = "Please write a review comment";
    } else {
        // Check if user already reviewed
        $check = $conn->query("SELECT * FROM reviews WHERE property_id = $property_id AND buyer_id = $buyer_id");
        if($check->num_rows > 0) {
            $error = "You have already reviewed this property";
        } else {
            $insert_query = "INSERT INTO reviews (property_id, buyer_id, rating, comment) 
                           VALUES ($property_id, $buyer_id, $rating, '$comment')";
            
            if($conn->query($insert_query)) {
                $success = "Thank you for your review!";
                header("refresh:2;url=property-details.php?id=$property_id");
            } else {
                $error = "Failed to submit review. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .star-rating-input {
            display: flex;
            gap: 0.5rem;
            font-size: 2rem;
            margin: 1rem 0;
        }
        .star-rating-input input[type="radio"] {
            display: none;
        }
        .star-rating-input label {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating-input input[type="radio"]:checked ~ label,
        .star-rating-input label:hover,
        .star-rating-input label:hover ~ label {
            color: #F39C12;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">🏠</span>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="properties.php">Properties</a></li>
                <li><a href="buyer-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center" style="margin-bottom: 1rem; color: #2C3E50;">Write a Review</h2>

            <!-- Property Summary -->
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h3 style="color: #2C3E50; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($property['property_name']); ?></h3>
                <p style="color: #7F8C8D; margin: 0;">📍 <?php echo htmlspecialchars($property['location'] . ', ' . $property['city']); ?></p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Your Rating: *</label>
                    <div class="star-rating-input">
                        <input type="radio" name="rating" id="star5" value="5" required>
                        <label for="star5">⭐</label>
                        <input type="radio" name="rating" id="star4" value="4">
                        <label for="star4">⭐</label>
                        <input type="radio" name="rating" id="star3" value="3">
                        <label for="star3">⭐</label>
                        <input type="radio" name="rating" id="star2" value="2">
                        <label for="star2">⭐</label>
                        <input type="radio" name="rating" id="star1" value="1">
                        <label for="star1">⭐</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="comment">Your Review: *</label>
                    <textarea name="comment" id="comment" required placeholder="Share your experience with this property..." style="min-height: 150px;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    ⭐ Submit Review
                </button>
            </form>
            
            <p class="text-center mt-2">
                <a href="property-details.php?id=<?php echo $property_id; ?>" style="color: #3498DB; font-weight: bold;">← Back to Property</a>
            </p>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
