<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete action
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE booking_id = $booking_id");
    header("Location: admin-bookings.php?msg=deleted");
    exit();
}

// Get all bookings
$query = "SELECT b.*, p.property_name, p.location, p.city, bu.fname, bu.lname, bu.email as buyer_email, o.name as owner_name
          FROM bookings b
          JOIN properties p ON b.property_id = p.property_id
          JOIN buyers bu ON b.buyer_id = bu.buyer_id
          JOIN owners o ON p.owner_id = o.owner_id
          ORDER BY b.created_at DESC";
$bookings = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin</title>
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
                <li><a href="admin-dashboard.php">Dashboard</a></li>
                <li><a href="admin-properties.php">Properties</a></li>
                <li><a href="admin-users.php">Users</a></li>
                <li><a href="admin-bookings.php" class="active">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="margin-bottom: 2rem;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Bookings</h1>
            <p style="color: #7F8C8D;">View and manage all property viewing bookings</p>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Booking deleted successfully!</div>
        <?php endif; ?>

        <?php if($bookings && $bookings->num_rows > 0): ?>
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Owner</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Buyer</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Date & Time</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;">#<?php echo $booking['booking_id']; ?></td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($booking['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($booking['location']); ?>, <?php echo htmlspecialchars($booking['city']); ?></small>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['owner_name']); ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($booking['buyer_email']); ?></small>
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
                                    <a href="?delete=<?php echo $booking['booking_id']; ?>" onclick="return confirm('Delete this booking?')" style="color: #E74C3C; text-decoration: none;">Delete</a>
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
                <p style="color: #7F8C8D;">Bookings will appear here once buyers start scheduling viewings</p>
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
