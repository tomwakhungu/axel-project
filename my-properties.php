<?php
require_once 'includes/config.php';

// Check if owner is logged in
if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Get owner's properties
$query = "SELECT * FROM properties WHERE owner_id = $owner_id ORDER BY created_at DESC";
$properties = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Properties - <?php echo SITE_NAME; ?></title>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">My Properties</h1>
                <p style="color: #7F8C8D;">Manage all your property listings</p>
            </div>
            <a href="add-property.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">➕</span> Add New Property
            </a>
        </div>

        <?php if($properties && $properties->num_rows > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                <?php while($property = $properties->fetch_assoc()): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <?php if(!empty($property['image_path']) && file_exists($property['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($property['image_path']); ?>" alt="<?php echo htmlspecialchars($property['property_name']); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
                                    <span style="font-size: 4rem;">🏠</span>
                                </div>
                            <?php endif; ?>
                            <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                                <?php echo $property['status']; ?>
                            </span>
                        </div>
                        <div class="property-content">
                            <h3><?php echo htmlspecialchars($property['property_name']); ?></h3>
                            <p class="property-location">📍 <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?></p>
                            <p class="property-description"><?php echo substr(htmlspecialchars($property['description']), 0, 100); ?>...</p>
                            
                            <?php if($property['bedrooms'] || $property['bathrooms'] || $property['area_sqft']): ?>
                            <div class="property-features">
                                <?php if($property['bedrooms']): ?>
                                    <span>🛏️ <?php echo $property['bedrooms']; ?> Beds</span>
                                <?php endif; ?>
                                <?php if($property['bathrooms']): ?>
                                    <span>🚿 <?php echo $property['bathrooms']; ?> Baths</span>
                                <?php endif; ?>
                                <?php if($property['area_sqft']): ?>
                                    <span>📐 <?php echo number_format($property['area_sqft']); ?> sqft</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E0E0E0;">
                                <div class="property-price">KES <?php echo number_format($property['price'], 2); ?></div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="edit-property.php?id=<?php echo $property['property_id']; ?>" class="btn" style="padding: 0.5rem 1rem; background: #3498DB; color: white; font-size: 0.9rem;">Edit</a>
                                    <a href="property-details.php?id=<?php echo $property['property_id']; ?>" class="btn" style="padding: 0.5rem 1rem; background: #27AE60; color: white; font-size: 0.9rem;">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 4rem; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🏠</div>
                <h2 style="color: #2C3E50; margin-bottom: 1rem;">No Properties Yet</h2>
                <p style="color: #7F8C8D; margin-bottom: 2rem;">Start adding properties to showcase them to potential buyers</p>
                <a href="add-property.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">➕</span> Add Your First Property
                </a>
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
