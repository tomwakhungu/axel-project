<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete property
if(isset($_GET['delete'])) {
    $property_id = intval($_GET['delete']);
    $conn->query("DELETE FROM properties WHERE property_id = $property_id");
    header("Location: manage-properties.php?success=deleted");
    exit();
}

// Get all properties
$properties_query = "SELECT p.*, o.name as owner_name, o.email as owner_email 
                     FROM properties p 
                     JOIN owners o ON p.owner_id = o.owner_id 
                     ORDER BY p.created_at DESC";
$properties = $conn->query($properties_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <div class="floating-bg">
        <div class="floating-icon">🏠</div>
        <div class="floating-icon">📊</div>
        <div class="floating-icon">💼</div>
        <div class="floating-icon">📈</div>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">🏠</span>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            <ul class="nav-menu">
                <li><a href="admin-dashboard.php">Dashboard</a></li>
                <li><a href="manage-properties.php" class="active">Properties</a></li>
                <li><a href="manage-owners.php">Owners</a></li>
                <li><a href="manage-buyers.php">Buyers</a></li>
                <li><a href="manage-bookings.php">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Manage Properties 🏘️</h1>
                <p style="color: #7F8C8D;">View and manage all properties</p>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Property deleted successfully!</div>
        <?php endif; ?>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <?php if($properties && $properties->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 5%;">ID</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Property Name</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Type</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 18%;">Location</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Price</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 12%;">Owner</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($property = $properties->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo $property['property_id']; ?></td>
                                <td style="padding: 1rem;"><strong><?php echo htmlspecialchars($property['property_name']); ?></strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['property_type']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?></td>
                                <td style="padding: 1rem;">KES <?php echo number_format($property['price'], 2); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['owner_name']); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                                        <?php echo $property['status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <a href="property-details.php?id=<?php echo $property['property_id']; ?>" style="color: #3498DB; text-decoration: none;">View</a>
                                        <a href="?delete=<?php echo $property['property_id']; ?>" style="color: #E74C3C; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🏠</div>
                    <p style="font-size: 1.2rem;">No properties found</p>
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
