<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle status update
if(isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE bookings SET status = '$new_status' WHERE booking_id = $booking_id");
    header("Location: manage-bookings.php?success=updated");
    exit();
}

// Handle delete booking
if(isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE booking_id = $booking_id");
    header("Location: manage-bookings.php?success=deleted");
    exit();
}

// Get all bookings
$bookings_query = "SELECT b.*, p.property_name, p.location, p.city, bu.fname, bu.lname, bu.email as buyer_email, o.name as owner_name 
                   FROM bookings b 
                   JOIN properties p ON b.property_id = p.property_id 
                   JOIN buyers bu ON b.buyer_id = bu.buyer_id 
                   JOIN owners o ON p.owner_id = o.owner_id 
                   ORDER BY b.created_at DESC";
$bookings = $conn->query($bookings_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <div class="floating-bg">
        <div class="floating-icon">📅</div>
        <div class="floating-icon">🏠</div>
        <div class="floating-icon">✅</div>
        <div class="floating-icon">📊</div>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">🏠</span>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            <ul class="nav-menu">
                <li><a href="admin-dashboard.php">Dashboard</a></li>
                <li><a href="manage-properties.php">Properties</a></li>
                <li><a href="manage-owners.php">Owners</a></li>
                <li><a href="manage-buyers.php">Buyers</a></li>
                <li><a href="manage-bookings.php" class="active">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Bookings 📅</h1>
                <p style="color: #7F8C8D;">View and manage all property viewing bookings</p>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_GET['success'] == 'updated' ? 'Booking status updated successfully!' : 'Booking deleted successfully!'; ?>
            </div>
        <?php endif; ?>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <?php if($bookings && $bookings->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Buyer</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Owner</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Date & Time</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo $booking['booking_id']; ?></td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($booking['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($booking['location']); ?>, <?php echo htmlspecialchars($booking['city']); ?></small>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($booking['buyer_email']); ?></small>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['owner_name']); ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?><br>
                                    <small style="color: #7F8C8D;"><?php echo date('h:i A', strtotime($booking['viewing_time'])); ?></small>
                                </td>
                                <td style="padding: 1rem;">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 0.5rem; border: 1px solid #E0E0E0; border-radius: 4px;">
                                            <option value="Pending" <?php echo $booking['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Confirmed" <?php echo $booking['status'] == 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="Completed" <?php echo $booking['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="Cancelled" <?php echo $booking['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="?delete=<?php echo $booking['booking_id']; ?>" style="color: #E74C3C; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📅</div>
                    <p style="font-size: 1.2rem;">No bookings found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
