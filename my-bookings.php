<?php
require_once 'includes/config.php';

// Check if owner is logged in
if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Get all bookings for owner's properties
$query = "SELECT b.*, p.property_name, p.location, p.city, bu.fname, bu.lname, bu.email as buyer_email, bu.phone_no as buyer_phone
          FROM bookings b
          JOIN properties p ON b.property_id = p.property_id
          JOIN buyers bu ON b.buyer_id = bu.buyer_id
          WHERE p.owner_id = $owner_id
          ORDER BY b.created_at DESC";
$bookings = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - <?php echo SITE_NAME; ?></title>
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
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="owner-dashboard.php" class="active">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="margin-bottom: 2rem;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">My Bookings</h1>
            <p style="color: #7F8C8D;">Manage viewing requests from potential buyers</p>
        </div>

        <?php if($bookings && $bookings->num_rows > 0): ?>
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Buyer</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Contact</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Date & Time</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($booking['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($booking['location']); ?>, <?php echo htmlspecialchars($booking['city']); ?></small>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></td>
                                <td style="padding: 1rem;">
                                    <small><?php echo htmlspecialchars($booking['buyer_email']); ?></small><br>
                                    <small><?php echo htmlspecialchars($booking['buyer_phone']); ?></small>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?><br>
                                    <small style="color: #7F8C8D;"><?php echo date('h:i A', strtotime($booking['viewing_time'])); ?></small>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="booking-details.php?id=<?php echo $booking['booking_id']; ?>" style="color: #3498DB; text-decoration: none;">View Details</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 4rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 5rem; margin-bottom: 1rem;">📅</div>
                <h2 style="color: #2C3E50; margin-bottom: 1rem;">No Bookings Yet</h2>
                <p style="color: #7F8C8D; margin-bottom: 2rem;">You haven't received any viewing requests yet</p>
                <a href="my-properties.php" class="btn btn-primary">View My Properties</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
