<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete actions
if(isset($_GET['delete_owner']) && is_numeric($_GET['delete_owner'])) {
    $owner_id = intval($_GET['delete_owner']);
    $conn->query("DELETE FROM owners WHERE owner_id = $owner_id");
    header("Location: admin-users.php?msg=owner_deleted");
    exit();
}

if(isset($_GET['delete_buyer']) && is_numeric($_GET['delete_buyer'])) {
    $buyer_id = intval($_GET['delete_buyer']);
    $conn->query("DELETE FROM buyers WHERE buyer_id = $buyer_id");
    header("Location: admin-users.php?msg=buyer_deleted");
    exit();
}

// Get all owners
$owners_query = "SELECT o.*, COUNT(p.property_id) as property_count 
                 FROM owners o 
                 LEFT JOIN properties p ON o.owner_id = p.owner_id 
                 GROUP BY o.owner_id 
                 ORDER BY o.created_at DESC";
$owners = $conn->query($owners_query);

// Get all buyers
$buyers_query = "SELECT b.*, COUNT(bk.booking_id) as booking_count 
                 FROM buyers b 
                 LEFT JOIN bookings bk ON b.buyer_id = bk.buyer_id 
                 GROUP BY b.buyer_id 
                 ORDER BY b.created_at DESC";
$buyers = $conn->query($buyers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                <li><a href="admin-users.php" class="active">Users</a></li>
                <li><a href="admin-bookings.php">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="margin-bottom: 2rem;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Users</h1>
            <p style="color: #7F8C8D;">View and manage all users on the platform</p>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">User deleted successfully!</div>
        <?php endif; ?>

        <!-- Property Owners -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Property Owners (<?php echo $owners->num_rows; ?>)</h2>
            
            <?php if($owners && $owners->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Name</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Email</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Phone</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Properties</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Joined</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($owner = $owners->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;">#<?php echo $owner['owner_id']; ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['email']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['mobile_no']); ?></td>
                                <td style="padding: 1rem;"><?php echo $owner['property_count']; ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($owner['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="?delete_owner=<?php echo $owner['owner_id']; ?>" onclick="return confirm('Delete this owner and all their properties?')" style="color: #E74C3C; text-decoration: none;">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #7F8C8D; text-align: center; padding: 2rem;">No owners yet</p>
            <?php endif; ?>
        </div>

        <!-- Buyers -->
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Buyers (<?php echo $buyers->num_rows; ?>)</h2>
            
            <?php if($buyers && $buyers->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Name</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Email</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Phone</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Bookings</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Joined</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($buyer = $buyers->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;">#<?php echo $buyer['buyer_id']; ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['fname'] . ' ' . $buyer['lname']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['email']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($buyer['phone_no']); ?></td>
                                <td style="padding: 1rem;"><?php echo $buyer['booking_count']; ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($buyer['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="?delete_buyer=<?php echo $buyer['buyer_id']; ?>" onclick="return confirm('Delete this buyer?')" style="color: #E74C3C; text-decoration: none;">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #7F8C8D; text-align: center; padding: 2rem;">No buyers yet</p>
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
