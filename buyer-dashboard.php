<?php
require_once 'includes/config.php';

if(!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Fetch buyer details
$buyer_query = "SELECT * FROM buyers WHERE buyer_id = $buyer_id";
$buyer = $conn->query($buyer_query)->fetch_assoc();

// Fetch bookings
$bookings_query = "SELECT b.*, p.property_name, p.location, p.city, p.price, p.image1, o.name as owner_name, o.mobile_no as owner_phone
                   FROM bookings b
                   JOIN properties p ON b.property_id = p.property_id
                   JOIN owners o ON p.owner_id = o.owner_id
                   WHERE b.buyer_id = $buyer_id
                   ORDER BY b.created_at DESC";
$bookings_result = $conn->query($bookings_query);

// Fetch reviews
$reviews_query = "SELECT r.*, p.property_name, p.property_id
                  FROM reviews r
                  JOIN properties p ON r.property_id = p.property_id
                  WHERE r.buyer_id = $buyer_id
                  ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_query);

// Statistics
$stats_query = "SELECT 
                (SELECT COUNT(*) FROM bookings WHERE buyer_id = $buyer_id) as total_bookings,
                (SELECT COUNT(*) FROM bookings WHERE buyer_id = $buyer_id AND status = 'Pending') as pending_bookings,
                (SELECT COUNT(*) FROM bookings WHERE buyer_id = $buyer_id AND status = 'Confirmed') as confirmed_bookings,
                (SELECT COUNT(*) FROM reviews WHERE buyer_id = $buyer_id) as total_reviews";
$stats = $conn->query($stats_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .sidebar {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li {
            margin-bottom: 1rem;
        }
        .sidebar-menu a {
            display: block;
            padding: 0.8rem 1rem;
            color: #2C3E50;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .booking-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1.5rem;
        }
        .booking-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .status-pending { background: #FFF3CD; color: #856404; }
        .status-confirmed { background: #D4EDDA; color: #155724; }
        .status-completed { background: #D1ECF1; color: #0C5460; }
        .status-cancelled { background: #F8D7DA; color: #721C24; }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .booking-card {
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
                <li><a href="buyer-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($buyer['fname']); ?>! 👋</h1>
        <p style="color: #7F8C8D; margin-bottom: 2rem;">Manage your property viewings and reviews</p>

        <div class="dashboard-grid">
            <!-- Sidebar -->
            <div class="sidebar">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498DB, #2980B9); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <?php echo strtoupper(substr($buyer['fname'], 0, 1)); ?>
                    </div>
                    <h3 style="color: #2C3E50; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($buyer['fname'] . ' ' . $buyer['lname']); ?></h3>
                    <p style="color: #7F8C8D; font-size: 0.9rem;"><?php echo htmlspecialchars($buyer['email']); ?></p>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="#overview" class="active" onclick="showSection('overview')">📊 Overview</a></li>
                    <li><a href="#bookings" onclick="showSection('bookings')">📅 My Bookings</a></li>
                    <li><a href="#reviews" onclick="showSection('reviews')">⭐ My Reviews</a></li>
                    <li><a href="#profile" onclick="showSection('profile')">👤 Profile</a></li>
                    <li><a href="properties.php">🏘️ Browse Properties</a></li>
                    <li><a href="logout.php" style="color: #E74C3C;">🚪 Logout</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Overview Section -->
                <div id="overview-section">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Dashboard Overview</h2>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" style="color: #3498DB;"><?php echo $stats['total_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Total Bookings</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #F39C12;"><?php echo $stats['pending_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Pending</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #27AE60;"><?php echo $stats['confirmed_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Confirmed</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #E74C3C;"><?php echo $stats['total_reviews']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Reviews Written</p>
                        </div>
                    </div>

                    <h3 style="color: #2C3E50; margin-bottom: 1rem;">Recent Bookings</h3>
                    <?php 
                    $recent_bookings = $conn->query("SELECT b.*, p.property_name, p.location, p.city, p.image1
                                                      FROM bookings b
                                                      JOIN properties p ON b.property_id = p.property_id
                                                      WHERE b.buyer_id = $buyer_id
                                                      ORDER BY b.created_at DESC LIMIT 3");
                    if($recent_bookings->num_rows > 0):
                        while($booking = $recent_bookings->fetch_assoc()):
                    ?>
                        <div class="booking-card">
                            <div class="booking-image">
                                <?php 
                                $img = $booking['image1'] ? 'uploads/properties/' . $booking['image1'] : 'assets/images/default-property.jpg';
                                ?>
                                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($booking['property_name']); ?>">
                            </div>
                            <div>
                                <h4 style="color: #2C3E50; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($booking['property_name']); ?></h4>
                                <p style="color: #7F8C8D; margin-bottom: 0.5rem;">📍 <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?></p>
                                <p style="color: #7F8C8D; margin-bottom: 0.5rem;">📅 <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('g:i A', strtotime($booking['viewing_time'])); ?></p>
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <p style="text-align: center; padding: 2rem; color: #7F8C8D;">No bookings yet. <a href="properties.php" style="color: #3498DB; font-weight: bold;">Browse properties</a> to get started!</p>
                    <?php endif; ?>
                </div>

                <!-- Bookings Section -->
                <div id="bookings-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">My Property Viewings</h2>
                    
                    <?php if($bookings_result->num_rows > 0): ?>
                        <?php while($booking = $bookings_result->fetch_assoc()): ?>
                            <div class="booking-card">
                                <div class="booking-image">
                                    <?php 
                                    $img = $booking['image1'] ? 'uploads/properties/' . $booking['image1'] : 'assets/images/default-property.jpg';
                                    ?>
                                    <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($booking['property_name']); ?>">
                                </div>
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                        <h3 style="color: #2C3E50;"><?php echo htmlspecialchars($booking['property_name']); ?></h3>
                                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                            <?php echo $booking['status']; ?>
                                        </span>
                                    </div>
                                    <p style="color: #7F8C8D; margin-bottom: 0.5rem;">📍 <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?></p>
                                    <p style="color: #E74C3C; font-weight: bold; margin-bottom: 0.5rem;">KES <?php echo number_format($booking['price'], 2); ?></p>
                                    <p style="color: #7F8C8D; margin-bottom: 0.5rem;">📅 <strong>Viewing Date:</strong> <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('g:i A', strtotime($booking['viewing_time'])); ?></p>
                                    <p style="color: #7F8C8D; margin-bottom: 0.5rem;">👤 <strong>Owner:</strong> <?php echo htmlspecialchars($booking['owner_name']); ?> | 📱 <?php echo htmlspecialchars($booking['owner_phone']); ?></p>
                                    <?php if($booking['message']): ?>
                                        <p style="color: #7F8C8D; margin-bottom: 0.5rem;">💬 <strong>Message:</strong> <?php echo htmlspecialchars($booking['message']); ?></p>
                                    <?php endif; ?>
                                    <p style="color: #95A5A6; font-size: 0.9rem;">Booked on: <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
                                    
                                    <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                                        <a href="property-details.php?id=<?php echo $booking['property_id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">View Property</a>
                                        <?php if($booking['status'] == 'Pending'): ?>
                                            <a href="cancel-booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 8px;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📅</div>
                            <h3 style="color: #7F8C8D; margin-bottom: 1rem;">No Bookings Yet</h3>
                            <p style="color: #95A5A6; margin-bottom: 2rem;">Start exploring properties and book your first viewing!</p>
                            <a href="properties.php" class="btn btn-primary" style="display: inline-block; padding: 1rem 2rem;">Browse Properties</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reviews Section -->
                <div id="reviews-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">My Reviews</h2>
                    
                    <?php if($reviews_result->num_rows > 0): ?>
                        <?php while($review = $reviews_result->fetch_assoc()): ?>
                            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="color: #2C3E50; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($review['property_name']); ?></h3>
                                        <a href="property-details.php?id=<?php echo $review['property_id']; ?>" style="color: #3498DB; font-size: 0.9rem;">View Property →</a>
                                    </div>
                                    <div style="color: #F39C12; font-size: 1.2rem;">
                                        <?php echo str_repeat('⭐', $review['rating']); ?>
                                    </div>
                                </div>
                                <p style="color: #7F8C8D; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($review['comment']); ?></p>
                                <p style="color: #95A5A6; font-size: 0.9rem;">Posted on: <?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 8px;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">⭐</div>
                            <h3 style="color: #7F8C8D; margin-bottom: 1rem;">No Reviews Yet</h3>
                            <p style="color: #95A5A6; margin-bottom: 2rem;">After viewing properties, come back and share your experience!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Profile Section -->
                <div id="profile-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">My Profile</h2>
                    
                    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">First Name:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($buyer['fname']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Last Name:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($buyer['lname']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Email:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($buyer['email']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Mobile:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($buyer['mobile_no']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Occupation:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($buyer['occupation'] ?: 'Not specified'); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Member Since:</label>
                                <p style="color: #7F8C8D;"><?php echo date('M d, Y', strtotime($buyer['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E0E0E0;">
                            <a href="edit-profile.php" class="btn btn-primary" style="display: inline-block; padding: 0.8rem 2rem;">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 4rem;">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        function showSection(section) {
            // Hide all sections
            document.getElementById('overview-section').style.display = 'none';
            document.getElementById('bookings-section').style.display = 'none';
            document.getElementById('reviews-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
            
            // Show selected section
            document.getElementById(section + '-section').style.display = 'block';
            
            // Update active menu item
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
