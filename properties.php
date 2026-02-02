<?php
require_once 'includes/config.php';

// Build query based on filters
$where_clauses = ["p.status = 'Available'"];
$params = [];

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clauses[] = "(p.property_name LIKE '%$search%' OR p.location LIKE '%$search%' OR p.city LIKE '%$search%')";
}

if(isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $conn->real_escape_string($_GET['type']);
    $where_clauses[] = "p.property_type = '$type'";
}

if(isset($_GET['price']) && !empty($_GET['price'])) {
    $price_range = explode('-', $_GET['price']);
    if(count($price_range) == 2) {
        $where_clauses[] = "p.price BETWEEN " . (int)$price_range[0] . " AND " . (int)$price_range[1];
    }
}

$where_sql = implode(' AND ', $where_clauses);
$query = "SELECT p.*, o.name as owner_name, o.mobile_no as owner_phone FROM properties p JOIN owners o ON p.owner_id = o.owner_id WHERE $where_sql ORDER BY p.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - <?php echo SITE_NAME; ?></title>
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
                <?php if(isset($_SESSION['buyer_id'])): ?>
                    <li><a href="buyer-dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif(isset($_SESSION['owner_id'])): ?>
                    <li><a href="owner-dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-primary">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div style="background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%); padding: 3rem 2rem; color: white;">
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 2rem;">Browse Available Properties</h1>
            
            <form action="properties.php" method="GET" class="search-box">
                <input type="text" name="search" placeholder="Search by location or property name..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <select name="type">
                    <option value="">Property Type</option>
                    <option value="Bungalow" <?php echo (isset($_GET['type']) && $_GET['type']=='Bungalow')?'selected':''; ?>>Bungalow</option>
                    <option value="Maisonette" <?php echo (isset($_GET['type']) && $_GET['type']=='Maisonette')?'selected':''; ?>>Maisonette</option>
                    <option value="Cottage" <?php echo (isset($_GET['type']) && $_GET['type']=='Cottage')?'selected':''; ?>>Cottage</option>
                    <option value="Apartment" <?php echo (isset($_GET['type']) && $_GET['type']=='Apartment')?'selected':''; ?>>Apartment</option>
                    <option value="Villa" <?php echo (isset($_GET['type']) && $_GET['type']=='Villa')?'selected':''; ?>>Villa</option>
                </select>
                <select name="price">
                    <option value="">Price Range</option>
                    <option value="0-5000000" <?php echo (isset($_GET['price']) && $_GET['price']=='0-5000000')?'selected':''; ?>>Under 5M</option>
                    <option value="5000000-10000000" <?php echo (isset($_GET['price']) && $_GET['price']=='5000000-10000000')?'selected':''; ?>>5M - 10M</option>
                    <option value="10000000-20000000" <?php echo (isset($_GET['price']) && $_GET['price']=='10000000-20000000')?'selected':''; ?>>10M - 20M</option>
                    <option value="20000000-999999999" <?php echo (isset($_GET['price']) && $_GET['price']=='20000000-999999999')?'selected':''; ?>>Above 20M</option>
                </select>
                <button type="submit">🔍 Search</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <h2>Found <?php echo $result->num_rows; ?> Properties</h2>
            <?php if(isset($_GET['search']) || isset($_GET['type']) || isset($_GET['price'])): ?>
                <a href="properties.php" class="btn btn-secondary">Clear Filters</a>
            <?php endif; ?>
        </div>

        <div class="properties-grid">
            <?php if($result->num_rows > 0): ?>
                <?php while($property = $result->fetch_assoc()): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <?php 
                            $image = $property['image1'] ? 'uploads/properties/' . $property['image1'] : 'assets/images/default-property.jpg';
                            ?>
                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($property['property_name']); ?>">
                            <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                                <?php echo $property['status']; ?>
                            </span>
                        </div>
                        <div class="property-content">
                            <h3 class="property-title"><?php echo htmlspecialchars($property['property_name']); ?></h3>
                            <p class="property-location">
                                📍 <?php echo htmlspecialchars($property['location'] . ', ' . $property['city']); ?>
                            </p>
                            <div style="background: #f8f9fa; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem;">
                                <small style="color: #6c757d;">Type: <strong><?php echo $property['property_type']; ?></strong></small>
                            </div>
                            <p class="property-price">KES <?php echo number_format($property['price'], 2); ?></p>
                            <div class="property-features">
                                <span>🛏️ <?php echo $property['bedrooms']; ?> Beds</span>
                                <span>🚿 <?php echo $property['bathrooms']; ?> Baths</span>
                                <span>📏 <?php echo number_format($property['square_feet']); ?> sqft</span>
                            </div>
                            <div class="property-actions">
                                <a href="property-details.php?id=<?php echo $property['property_id']; ?>" class="btn btn-primary">View Details</a>
                                <?php if(isset($_SESSION['buyer_id'])): ?>
                                    <a href="book-viewing.php?id=<?php echo $property['property_id']; ?>" class="btn btn-secondary">Book Viewing</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🏚️</div>
                    <h2 style="color: #7F8C8D; margin-bottom: 1rem;">No Properties Found</h2>
                    <p style="color: #95A5A6; margin-bottom: 2rem;">Try adjusting your search filters</p>
                    <a href="properties.php" class="btn btn-primary">View All Properties</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>Your trusted partner in finding the perfect property in Kenya.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php">Home</a></p>
                <p><a href="properties.php">Properties</a></p>
                <p><a href="about.php">About Us</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
