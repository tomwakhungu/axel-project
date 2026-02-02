<?php
require_once 'includes/config.php';

if(!isset($_GET['id'])) {
    header("Location: properties.php");
    exit();
}

$property_id = (int)$_GET['id'];

// Fetch property details
$query = "SELECT p.*, o.name as owner_name, o.mobile_no as owner_phone, o.email as owner_email
          FROM properties p 
          JOIN owners o ON p.owner_id = o.owner_id 
          WHERE p.property_id = $property_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: properties.php");
    exit();
}

$property = $result->fetch_assoc();

// Fetch reviews
$reviews_query = "SELECT r.*, b.fname, b.lname 
                  FROM reviews r 
                  JOIN buyers b ON r.buyer_id = b.buyer_id 
                  WHERE r.property_id = $property_id 
                  ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_query);

// Calculate average rating
$avg_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
              FROM reviews WHERE property_id = $property_id";
$avg_result = $conn->query($avg_query)->fetch_assoc();
$avg_rating = $avg_result['avg_rating'] ? round($avg_result['avg_rating'], 1) : 0;
$total_reviews = $avg_result['total_reviews'];

// Similar properties
$similar_query = "SELECT p.*, o.name as owner_name 
                  FROM properties p 
                  JOIN owners o ON p.owner_id = o.owner_id 
                  WHERE p.property_type = '{$property['property_type']}' 
                  AND p.property_id != $property_id 
                  AND p.status = 'Available'
                  LIMIT 3";
$similar_result = $conn->query($similar_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['property_name']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .image-gallery {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .main-image {
            grid-row: span 2;
            height: 500px;
            border-radius: 8px;
            overflow: hidden;
        }
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .thumb-image {
            height: 240px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        }
        .thumb-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .thumb-image:hover img {
            transform: scale(1.1);
        }
        .property-details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .amenities-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .contact-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }
        .review-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .star-rating {
            color: #F39C12;
            font-size: 1.2rem;
        }
        @media (max-width: 768px) {
            .image-gallery {
                grid-template-columns: 1fr;
            }
            .property-details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <!-- Breadcrumb -->
        <div style="margin-bottom: 2rem; color: #7F8C8D;">
            <a href="index.php" style="color: #3498DB;">Home</a> / 
            <a href="properties.php" style="color: #3498DB;">Properties</a> / 
            <span><?php echo htmlspecialchars($property['property_name']); ?></span>
        </div>

        <!-- Image Gallery -->
        <div class="image-gallery">
            <div class="main-image" id="main-image">
                <?php 
                $main_image = $property['image1'] ? 'uploads/properties/' . $property['image1'] : 'assets/images/default-property.jpg';
                ?>
                <img src="<?php echo $main_image; ?>" alt="<?php echo htmlspecialchars($property['property_name']); ?>">
            </div>
            <?php if($property['image2']): ?>
                <div class="thumb-image" onclick="changeMainImage('uploads/properties/<?php echo $property['image2']; ?>')">
                    <img src="uploads/properties/<?php echo $property['image2']; ?>" alt="Property Image 2">
                </div>
            <?php endif; ?>
            <?php if($property['image3']): ?>
                <div class="thumb-image" onclick="changeMainImage('uploads/properties/<?php echo $property['image3']; ?>')">
                    <img src="uploads/properties/<?php echo $property['image3']; ?>" alt="Property Image 3">
                </div>
            <?php endif; ?>
        </div>

        <!-- Property Details Grid -->
        <div class="property-details-grid">
            <div>
                <!-- Property Header -->
                <div style="margin-bottom: 2rem;">
                    <h1 style="color: #2C3E50; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($property['property_name']); ?></h1>
                    <p style="color: #7F8C8D; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                        📍 <?php echo htmlspecialchars($property['location'] . ', ' . $property['city'] . ', ' . $property['state']); ?>
                    </p>
                    <div style="display: flex; align-items: center; gap: 2rem; margin-top: 1rem;">
                        <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                            <?php echo $property['status']; ?>
                        </span>
                        <span style="color: #7F8C8D;">Type: <strong><?php echo $property['property_type']; ?></strong></span>
                        <?php if($total_reviews > 0): ?>
                            <span class="star-rating">
                                ⭐ <?php echo $avg_rating; ?> (<?php echo $total_reviews; ?> reviews)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Price -->
                <div style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%); color: white; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h2 style="font-size: 2.5rem; margin: 0;">KES <?php echo number_format($property['price'], 2); ?></h2>
                    <p style="margin: 0; opacity: 0.9;">Negotiable</p>
                </div>

                <!-- Key Features -->
                <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: #2C3E50;">Key Features</h3>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🛏️</div>
                            <p style="font-size: 1.5rem; font-weight: bold; color: #2C3E50; margin: 0;"><?php echo $property['bedrooms']; ?></p>
                            <p style="color: #7F8C8D; margin: 0;">Bedrooms</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🚿</div>
                            <p style="font-size: 1.5rem; font-weight: bold; color: #2C3E50; margin: 0;"><?php echo $property['bathrooms']; ?></p>
                            <p style="color: #7F8C8D; margin: 0;">Bathrooms</p>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📏</div>
                            <p style="font-size: 1.5rem; font-weight: bold; color: #2C3E50; margin: 0;"><?php echo number_format($property['square_feet']); ?></p>
                            <p style="color: #7F8C8D; margin: 0;">Square Feet</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: #2C3E50;">Description</h3>
                    <p style="color: #7F8C8D; line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                    </p>
                </div>

                <!-- Amenities -->
                <?php if($property['amenities']): ?>
                <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: #2C3E50;">Amenities</h3>
                    <div class="amenities-list">
                        <?php 
                        $amenities = explode(',', $property['amenities']);
                        foreach($amenities as $amenity): 
                        ?>
                            <div class="amenity-item">
                                <span>✓</span>
                                <span><?php echo trim($amenity); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: #2C3E50;">Reviews & Ratings</h3>
                    
                    <?php if($total_reviews > 0): ?>
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="text-align: center;">
                                <div style="font-size: 3rem; font-weight: bold; color: #F39C12;"><?php echo $avg_rating; ?></div>
                                <div class="star-rating">⭐⭐⭐⭐⭐</div>
                                <div style="color: #7F8C8D; margin-top: 0.5rem;"><?php echo $total_reviews; ?> reviews</div>
                            </div>
                        </div>

                        <?php while($review = $reviews_result->fetch_assoc()): ?>
                            <div class="review-card">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <strong style="color: #2C3E50;"><?php echo htmlspecialchars($review['fname'] . ' ' . $review['lname']); ?></strong>
                                    <span class="star-rating">
                                        <?php echo str_repeat('⭐', $review['rating']); ?>
                                    </span>
                                </div>
                                <p style="color: #7F8C8D; margin: 0;"><?php echo htmlspecialchars($review['comment']); ?></p>
                                <small style="color: #95A5A6;"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #7F8C8D; text-align: center; padding: 2rem;">No reviews yet. Be the first to review this property!</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="contact-card">
                    <h3 style="margin-bottom: 1.5rem; color: #2C3E50;">Contact Property Owner</h3>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <p style="font-weight: bold; color: #2C3E50; margin-bottom: 0.5rem;">Owner:</p>
                        <p style="color: #7F8C8D;"><?php echo htmlspecialchars($property['owner_name']); ?></p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <p style="font-weight: bold; color: #2C3E50; margin-bottom: 0.5rem;">📱 Phone:</p>
                        <a href="tel:<?php echo $property['owner_phone']; ?>" style="color: #3498DB; font-weight: 600;">
                            <?php echo htmlspecialchars($property['owner_phone']); ?>
                        </a>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <p style="font-weight: bold; color: #2C3E50; margin-bottom: 0.5rem;">📧 Email:</p>
                        <a href="mailto:<?php echo $property['owner_email']; ?>" style="color: #3498DB; font-weight: 600; word-break: break-all;">
                            <?php echo htmlspecialchars($property['owner_email']); ?>
                        </a>
                    </div>

                    <?php if(isset($_SESSION['buyer_id']) && $property['status'] == 'Available'): ?>
                        <a href="book-viewing.php?id=<?php echo $property_id; ?>" class="btn btn-primary" style="display: block; text-align: center; padding: 1rem; margin-bottom: 1rem;">
                            📅 Book a Viewing
                        </a>
                        <a href="add-review.php?id=<?php echo $property_id; ?>" class="btn btn-secondary" style="display: block; text-align: center; padding: 1rem;">
                            ⭐ Write a Review
                        </a>
                    <?php elseif(!isset($_SESSION['buyer_id']) && !isset($_SESSION['owner_id'])): ?>
                        <a href="login.php" class="btn btn-primary" style="display: block; text-align: center; padding: 1rem;">
                            Login to Book Viewing
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Similar Properties -->
                <?php if($similar_result->num_rows > 0): ?>
                <div style="margin-top: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: #2C3E50;">Similar Properties</h3>
                    <?php while($similar = $similar_result->fetch_assoc()): ?>
                        <a href="property-details.php?id=<?php echo $similar['property_id']; ?>" style="display: block; margin-bottom: 1rem;">
                            <div style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.3s;">
                                <?php 
                                $sim_image = $similar['image1'] ? 'uploads/properties/' . $similar['image1'] : 'assets/images/default-property.jpg';
                                ?>
                                <img src="<?php echo $sim_image; ?>" alt="<?php echo htmlspecialchars($similar['property_name']); ?>" style="width: 100%; height: 150px; object-fit: cover;">
                                <div style="padding: 1rem;">
                                    <h4 style="color: #2C3E50; margin-bottom: 0.5rem; font-size: 1rem;"><?php echo htmlspecialchars($similar['property_name']); ?></h4>
                                    <p style="color: #E74C3C; font-weight: bold; margin: 0;">KES <?php echo number_format($similar['price'], 2); ?></p>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        function changeMainImage(src) {
            document.querySelector('#main-image img').src = src;
        }
    </script>
</body>
</html>
