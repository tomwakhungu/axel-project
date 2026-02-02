<?php
require_once 'includes/config.php';

// Fetch featured properties
$featured_query = "SELECT p.*, o.name as owner_name, o.mobile_no as owner_phone 
                   FROM properties p 
                   JOIN owners o ON p.owner_id = o.owner_id 
                   WHERE p.status = 'Available' 
                   ORDER BY p.created_at DESC 
                   LIMIT 6";
$featured_result = $conn->query($featured_query);

// Get property statistics
$stats_query = "SELECT 
                (SELECT COUNT(*) FROM properties WHERE status='Available') as available,
                (SELECT COUNT(*) FROM properties WHERE status='Sold') as sold,
                (SELECT COUNT(*) FROM owners) as owners,
                (SELECT COUNT(*) FROM buyers) as buyers";
$stats = $conn->query($stats_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Find Your Dream Property</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
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

    <!-- Hero Section -->
    <section class="hero">
        <h1>Find Your Dream Home in Kenya</h1>
        <p>Discover the perfect property that matches your lifestyle and budget</p>
        
        <form action="properties.php" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search by location or property name...">
            <select name="type">
                <option value="">Property Type</option>
                <option value="Bungalow">Bungalow</option>
                <option value="Maisonette">Maisonette</option>
                <option value="Cottage">Cottage</option>
                <option value="Apartment">Apartment</option>
                <option value="Villa">Villa</option>
            </select>
            <select name="price">
                <option value="">Price Range</option>
                <option value="0-5000000">Under 5M</option>
                <option value="5000000-10000000">5M - 10M</option>
                <option value="10000000-20000000">10M - 20M</option>
                <option value="20000000-999999999">Above 20M</option>
            </select>
            <button type="submit">🔍 Search</button>
        </form>
    </section>

    <!-- Statistics Section -->
    <section class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center; margin-bottom: 3rem;">
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2.5rem; color: #27AE60; margin-bottom: 0.5rem;"><?php echo $stats['available']; ?></h3>
                <p style="color: #7F8C8D; font-weight: 600;">Available Properties</p>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2.5rem; color: #E74C3C; margin-bottom: 0.5rem;"><?php echo $stats['sold']; ?></h3>
                <p style="color: #7F8C8D; font-weight: 600;">Properties Sold</p>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2.5rem; color: #3498DB; margin-bottom: 0.5rem;"><?php echo $stats['owners']; ?></h3>
                <p style="color: #7F8C8D; font-weight: 600;">Property Owners</p>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="font-size: 2.5rem; color: #F39C12; margin-bottom: 0.5rem;"><?php echo $stats['buyers']; ?></h3>
                <p style="color: #7F8C8D; font-weight: 600;">Registered Buyers</p>
            </div>
        </div>

        <!-- Featured Properties -->
        <h2 class="section-title">Featured Properties</h2>
        <div class="properties-grid">
            <?php if($featured_result->num_rows > 0): ?>
                <?php while($property = $featured_result->fetch_assoc()): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <?php 
                            $image = !empty($property['image1']) ? 'uploads/properties/' . $property['image1'] : 'assets/images/default-property.jpg';
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
                            <p class="property-price">KES <?php echo number_format($property['price'], 2); ?></p>
                            <div class="property-features">
                                <span>🛏️ <?php echo isset($property['bedrooms']) ? $property['bedrooms'] : 'N/A'; ?> Beds</span>
                                <span>🚿 <?php echo isset($property['bathrooms']) ? $property['bathrooms'] : 'N/A'; ?> Baths</span>
                                <span>📏 <?php echo isset($property['square_feet']) ? number_format($property['square_feet']) : 'N/A'; ?> sqft</span>
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
                <p class="text-center" style="grid-column: 1/-1;">No properties available at the moment.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-2">
            <a href="properties.php" class="btn btn-primary" style="display: inline-block; padding: 1rem 2rem;">View All Properties</a>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="container">
        <h2 class="section-title">Why Choose Axel Real Estate?</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                <h3 style="margin-bottom: 1rem; color: #2C3E50;">Verified Properties</h3>
                <p style="color: #7F8C8D;">All listings are verified for authenticity and accuracy</p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
                <h3 style="margin-bottom: 1rem; color: #2C3E50;">Secure Transactions</h3>
                <p style="color: #7F8C8D;">Your data and transactions are completely secure</p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💼</div>
                <h3 style="margin-bottom: 1rem; color: #2C3E50;">Expert Support</h3>
                <p style="color: #7F8C8D;">24/7 customer support to help you find your dream home</p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                <h3 style="margin-bottom: 1rem; color: #2C3E50;">Fast & Easy</h3>
                <p style="color: #7F8C8D;">Quick booking and seamless property viewing process</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>Your trusted partner in finding the perfect property in Kenya.</p>
                <p>📧 <?php echo SITE_EMAIL; ?></p>
                <p>📱 <?php echo SITE_PHONE; ?></p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php">Home</a></p>
                <p><a href="properties.php">Properties</a></p>
                <p><a href="about.php">About Us</a></p>
                <p><a href="contact.php">Contact</a></p>
            </div>
            <div class="footer-section">
                <h3>For Property Owners</h3>
                <p><a href="register.php?type=owner">List Your Property</a></p>
                <p><a href="owner-dashboard.php">Owner Dashboard</a></p>
            </div>
            <div class="footer-section">
                <h3>For Buyers</h3>
                <p><a href="register.php?type=buyer">Create Account</a></p>
                <p><a href="properties.php">Browse Properties</a></p>
                <p><a href="buyer-dashboard.php">My Dashboard</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved. | Developed by Jeremy Mwimba Kahozi</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
