<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete owner
if(isset($_GET['delete'])) {
    $owner_id = intval($_GET['delete']);
    // First delete all properties of this owner
    $conn->query("DELETE FROM properties WHERE owner_id = $owner_id");
    // Then delete the owner
    $conn->query("DELETE FROM owners WHERE owner_id = $owner_id");
    header("Location: manage-owners.php?success=deleted");
    exit();
}

// Get all owners with property count
$owners_query = "SELECT o.*, COUNT(p.property_id) as property_count 
                 FROM owners o 
                 LEFT JOIN properties p ON o.owner_id = p.owner_id 
                 GROUP BY o.owner_id 
                 ORDER BY o.owner_id DESC";
$owners = $conn->query($owners_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Owners - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <div class="floating-bg">
        <div class="floating-icon">👥</div>
        <div class="floating-icon">🏠</div>
        <div class="floating-icon">💼</div>
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
                <li><a href="manage-owners.php" class="active">Owners</a></li>
                <li><a href="manage-buyers.php">Buyers</a></li>
                <li><a href="manage-bookings.php">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Property Owners 👥</h1>
                <p style="color: #7F8C8D;">View and manage all property owners</p>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Owner deleted successfully!</div>
        <?php endif; ?>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <?php if($owners && $owners->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 5%;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 18%;">Name</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Email</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Mobile</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Address</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 12%;">Properties</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($owner = $owners->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo $owner['owner_id']; ?></td>
                                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($owner['name']); ?></strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['email']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['mobile_no']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($owner['address']); ?></td>
                                <td style="padding: 1rem;">
                                    <span style="background: #3498DB; color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                        <?php echo $owner['property_count']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="?delete=<?php echo $owner['owner_id']; ?>" style="color: #E74C3C; text-decoration: none;" onclick="return confirm('Are you sure? This will delete all properties of this owner!');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">👥</div>
                    <p style="font-size: 1.2rem;">No property owners found</p>
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
