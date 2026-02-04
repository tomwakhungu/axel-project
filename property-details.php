<?php
require_once 'includes/config.php';

// Get property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($property_id == 0) {
    header("Location: properties.php");
    exit();
}

// Get property details with owner information
$query = "SELECT p.*, o.name as owner_name, o.email as owner_email, o.mobile_no as owner_phone 
          FROM properties p 
          JOIN owners o ON p.owner_id = o.owner_id 
          WHERE p.property_id = $property_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: properties.php");
    exit();
}

$property = $result->fetch_assoc();

// Split amenities into array
$amenities = !empty($property['amenities']) ? explode(',', $property['amenities']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['property_name']); ?> - <?php echo SITE_NAME; ?></title>
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
                <li><a href="properties.php" class="active">Properties</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION['buyer_id'])): ?>
                    <li><a href="buyer-dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif(isset($_SESSION['owner_id'])): ?>
                    <li><a href="owner-dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif(isset($_SESSION['admin_id'])): ?>
                    <li><a href="admin-dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <a href="properties.php" style="display: inline-block; color: #E74C3C; text-decoration: none; margin-bottom: 2rem;">
            ← Back to Properties
        </a>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left Column - Property Details -->
            <div>
                <!-- Property Image -->
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <?php if(!empty($property['image_path']) && file_exists($property['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($property['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($property['property_name']); ?>"
                             style="width: 100%; height: 500px; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 500px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <span style="font-size: 8rem;">🏠</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Property Information -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <div style="margin-bottom: 2rem;">
                        <h1 style="color: #2C3E50; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($property['property_name']); ?></h1>
                        <p style="color: #7F8C8D; font-size: 1.1rem;">
                            📍 <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?>
                            <?php if(!empty($property['state'])): ?>, <?php echo htmlspecialchars($property['state']); ?><?php endif; ?>
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%); border-radius: 8px; margin-bottom: 2rem;">
                        <div>
                            <div style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 0.5rem;">Price</div>
                            <div style="color: white; font-size: 2.5rem; font-weight: bold;">KES <?php echo number_format($property['price'], 2); ?></div>
                            <div style="color: rgba(255,255,255,0.9); font-size: 0.9rem;">Negotiable</div>
                        </div>
                        <span class="property-status status-<?php echo strtolower($property['status']); ?>" style="font-size: 1.1rem; padding: 0.75rem 1.5rem;">
                            <?php echo $property['status']; ?>
                        </span>
                    </div>

                    <!-- Key Features -->
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Key Features</h2>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                        <?php if($property['bedrooms']): ?>
                        <div style="text-align: center; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛏️</div>
                            <div style="font-size: 2rem; font-weight: bold; color: #2C3E50;"><?php echo $property['bedrooms']; ?></div>
                            <div style="color: #7F8C8D;">Bedrooms</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($property['bathrooms']): ?>
                        <div style="text-align: center; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🚿</div>
                            <div style="font-size: 2rem; font-weight: bold; color: #2C3E50;"><?php echo $property['bathrooms']; ?></div>
                            <div style="color: #7F8C8D;">Bathrooms</div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($property['area_sqft']): ?>
                        <div style="text-align: center; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">📐</div>
                            <div style="font-size: 2rem; font-weight: bold; color: #2C3E50;"><?php echo number_format($property['area_sqft']); ?></div>
                            <div style="color: #7F8C8D;">Square Feet</div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <h2 style="color: #2C3E50; margin-bottom: 1rem;">Description</h2>
                    <p style="color: #555; line-height: 1.8; margin-bottom: 2rem;">
                        <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                    </p>

                    <!-- Property Details -->
                    <h2 style="color: #2C3E50; margin-bottom: 1rem;">Property Details</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7F8C8D; font-size: 0.9rem; margin-bottom: 0.5rem;">Property Type</div>
                            <div style="color: #2C3E50; font-weight: 600;"><?php echo htmlspecialchars($property['property_type']); ?></div>
                        </div>
                        
                        <?php if($property['year_built']): ?>
                        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7F8C8D; font-size: 0.9rem; margin-bottom: 0.5rem;">Year Built</div>
                            <div style="color: #2C3E50; font-weight: 600;"><?php echo $property['year_built']; ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($property['zip_code'])): ?>
                        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7F8C8D; font-size: 0.9rem; margin-bottom: 0.5rem;">Zip Code</div>
                            <div style="color: #2C3E50; font-weight: 600;"><?php echo htmlspecialchars($property['zip_code']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7F8C8D; font-size: 0.9rem; margin-bottom: 0.5rem;">Listed</div>
                            <div style="color: #2C3E50; font-weight: 600;"><?php echo date('F d, Y', strtotime($property['created_at'])); ?></div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <?php if(!empty($amenities)): ?>
                    <h2 style="color: #2C3E50; margin-bottom: 1rem;">Amenities</h2>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <?php foreach($amenities as $amenity): ?>
                        <div style="padding: 0.75rem; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; gap: 0.5rem;">
                            <span style="color: #27AE60; font-size: 1.2rem;">✓</span>
                            <span style="color: #2C3E50;"><?php echo trim(htmlspecialchars($amenity)); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Contact Owner (Sticky) -->
            <div>
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: sticky; top: 2rem;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Contact Property Owner</h2>
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 8px; text-align: center; margin-bottom: 1.5rem;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">👤</div>
                            <h3 style="margin: 0;"><?php echo htmlspecialchars($property['owner_name']); ?></h3>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <span style="font-size: 1.5rem;">📞</span>
                                <div>
                                    <div style="color: #7F8C8D; font-size: 0.85rem;">Phone:</div>
                                    <a href="tel:<?php echo htmlspecialchars($property['owner_phone']); ?>" style="color: #3498DB; text-decoration: none; font-weight: 600;">
                                        <?php echo htmlspecialchars($property['owner_phone']); ?>
                                    </a>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                <span style="font-size: 1.5rem;">📧</span>
                                <div style="word-break: break-word;">
                                    <div style="color: #7F8C8D; font-size: 0.85rem;">Email:</div>
                                    <a href="mailto:<?php echo htmlspecialchars($property['owner_email']); ?>" style="color: #3498DB; text-decoration: none; font-weight: 600;">
                                        <?php echo htmlspecialchars($property['owner_email']); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(isset($_SESSION['buyer_id'])): ?>
                        <a href="book-viewing.php?property_id=<?php echo $property['property_id']; ?>" class="btn btn-primary btn-block" style="display: block; text-align: center; text-decoration: none;">
                            📅 Book a Viewing
                        </a>
                    <?php else: ?>
                        <a href="login.php?redirect=property-details.php?id=<?php echo $property['property_id']; ?>" class="btn btn-primary btn-block" style="display: block; text-align: center; text-decoration: none;">
                            Login to Book Viewing
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
