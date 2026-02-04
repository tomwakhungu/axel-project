<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete buyer
if(isset($_GET['delete'])) {
    $buyer_id = intval($_GET['delete']);
    // First delete all bookings of this buyer
    $conn->query("DELETE FROM bookings WHERE buyer_id = $buyer_id");
    // Then delete the buyer
    $conn->query("DELETE FROM buyers WHERE buyer_id = $buyer_id");
    header("Location: manage-buyers.php?success=deleted");
    exit();
}

// Get all buyers with booking count
$buyers_query = "SELECT b.*, COUNT(bk.booking_id) as booking_count 
                 FROM buyers b 
                 LEFT JOIN bookings bk ON b.buyer_id = bk.buyer_id 
                 GROUP BY b.buyer_id 
                 ORDER BY b.buyer_id DESC";
$buyers = $conn->query($buyers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buyers - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <div class="floating-bg">
        <div class="floating-icon">🛒</div>
        <div class="floating-icon">👥</div>
        <div class="floating-icon">📅</div>
        <div class="floating-icon">🏠</div>
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
                <li><a href="manage-buyers.php" class="active">Buyers</a></li>
                <li><a href="manage-bookings.php">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Buyers 🛒</h1>
                <p style="color: #7F8C8D;">View and manage all buyers</p>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Buyer deleted successfully!</div>
        <?php endif; ?>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <?php if($buyers && $buyers->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 5%;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 18%;">Name</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 22%;">Email</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Phone</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Address</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Bookings</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($buyer = $buyers->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo $buyer['buyer_id']; ?></td>
                                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($buyer['fname'] . ' ' . $buyer['lname']); ?></strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['email']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['phone_no']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['address']); ?></td>
                                <td style="padding: 1rem;">
                                    <span style="background: #9B59B6; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                        <?php echo $buyer['booking_count']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="?delete=<?php echo $buyer['buyer_id']; ?>" style="color: #E74C3C; text-decoration: none;" onclick="return confirm('Are you sure? This will delete all bookings of this buyer!');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
                    <p style="font-size: 1.2rem;">No buyers found</p>
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
