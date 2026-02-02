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
    $viewing_date = $conn->real_escape_string($_POST['viewing_date']);
    $viewing_time = $conn->real_escape_string($_POST['viewing_time']);
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    if(empty($viewing_date) || empty($viewing_time)) {
        $error = "Please select a date and time for the viewing";
    } else {
        // Check if date is in the future
        if(strtotime($viewing_date) < strtotime(date('Y-m-d'))) {
            $error = "Please select a future date";
        } else {
            $insert_query = "INSERT INTO bookings (property_id, buyer_id, viewing_date, viewing_time, message, status) 
                           VALUES ($property_id, $buyer_id, '$viewing_date', '$viewing_time', '$message', 'Pending')";
            
            if($conn->query($insert_query)) {
                $success = "Viewing booked successfully! The property owner will contact you soon.";
                header("refresh:2;url=buyer-dashboard.php");
            } else {
                $error = "Failed to book viewing. Please try again.";
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
    <title>Book Viewing - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
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
            <h2 class="text-center" style="margin-bottom: 1rem; color: #2C3E50;">Book a Property Viewing</h2>
            
            <!-- Property Summary -->
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h3 style="color: #2C3E50; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($property['property_name']); ?></h3>
                <p style="color: #7F8C8D; margin-bottom: 0.5rem;">📍 <?php echo htmlspecialchars($property['location'] . ', ' . $property['city']); ?></p>
                <p style="color: #E74C3C; font-weight: bold; font-size: 1.3rem; margin: 0;">KES <?php echo number_format($property['price'], 2); ?></p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="viewing_date">Preferred Viewing Date: *</label>
                    <input type="date" id="viewing_date" name="viewing_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="viewing_time">Preferred Time: *</label>
                    <select name="viewing_time" id="viewing_time" required>
                        <option value="">Select Time</option>
                        <option value="09:00:00">9:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="12:00:00">12:00 PM</option>
                        <option value="13:00:00">1:00 PM</option>
                        <option value="14:00:00">2:00 PM</option>
                        <option value="15:00:00">3:00 PM</option>
                        <option value="16:00:00">4:00 PM</option>
                        <option value="17:00:00">5:00 PM</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="message">Additional Message (Optional):</label>
                    <textarea name="message" id="message" placeholder="Any special requests or questions..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    📅 Confirm Booking
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
