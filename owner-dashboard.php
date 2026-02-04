<?php
require_once 'includes/config.php';

// Check if owner is logged in
if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Get owner information
$owner_query = "SELECT * FROM owners WHERE owner_id = $owner_id";
$owner_result = $conn->query($owner_query);
$owner = $owner_result->fetch_assoc();

// Get owner's properties count
$properties_query = "SELECT COUNT(*) as total_properties FROM properties WHERE owner_id = $owner_id";
$properties_result = $conn->query($properties_query);
$properties_count = $properties_result->fetch_assoc()['total_properties'];

// Get owner's properties
$properties_list_query = "SELECT * FROM properties WHERE owner_id = $owner_id ORDER BY created_at DESC";
$properties_list = $conn->query($properties_list_query);

// Get recent bookings for owner's properties
$bookings_query = "SELECT b.*, p.property_name, p.location, bu.fname, bu.lname, bu.email as buyer_email, bu.phone_no as buyer_phone
                   FROM bookings b
                   JOIN properties p ON b.property_id = p.property_id
                   JOIN buyers bu ON b.buyer_id = bu.buyer_id
                   WHERE p.owner_id = $owner_id
                   ORDER BY b.created_at DESC
                   LIMIT 10";
$bookings = $conn->query($bookings_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Floating Background -->
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
        <div style="margin-bottom: 2rem; animation: fadeInDown 0.6s ease-out;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($owner['name']); ?>! 👋</h1>
            <p style="color: #7F8C8D;">Manage your properties and bookings</p>
        </div>

        <!-- Dashboard Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">🏠</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $properties_count; ?></div>
                <div style="opacity: 0.9;">Total Properties</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">📅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $bookings ? $bookings->num_rows : 0; ?></div>
                <div style="opacity: 0.9;">Total Bookings</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;">
                    <?php
                    $available_query = "SELECT COUNT(*) as count FROM properties WHERE owner_id = $owner_id AND status = 'Available'";
                    $available_result = $conn->query($available_query);
                    echo $available_result->fetch_assoc()['count'];
                    ?>
                </div>
                <div style="opacity: 0.9;">Available Properties</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">⚡ Quick Actions</h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="add-property.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;" data-tooltip="Add a new property listing">
                    <span style="font-size: 1.2rem;">➕</span> Add New Property
                </a>
                <a href="my-properties.php" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #34495E; color: white;" data-tooltip="View all your properties">
                    <span style="font-size: 1.2rem;">📋</span> View My Properties
                </a>
                <a href="my-bookings.php" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #16A085; color: white;" data-tooltip="Manage property viewings">
                    <span style="font-size: 1.2rem;">📅</span> View Bookings
                </a>
            </div>
        </div>

        <!-- Recent Properties -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">🏘️ My Properties</h2>
            
            <?php if($properties_list && $properties_list->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 25%;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 12%;">Type</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Location</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Price</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 10%;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 18%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($property = $properties_list->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['property_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['property_type']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?></td>
                                <td style="padding: 1rem;">KES <?php echo number_format($property['price'], 2); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                                        <?php echo $property['status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <a href="edit-property.php?id=<?php echo $property['property_id']; ?>" style="color: #3498DB; text-decoration: none;" data-tooltip="Edit this property">Edit</a>
                                        <a href="property-details.php?id=<?php echo $property['property_id']; ?>" style="color: #27AE60; text-decoration: none;" data-tooltip="View property details">View</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem; animation: bounce 2s ease-in-out infinite;">🏠</div>
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">No properties yet</p>
                    <a href="add-property.php" class="btn btn-primary">Add Your First Property</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Bookings -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">📅 Recent Bookings</h2>
            
            <?php if($bookings && $bookings->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 25%;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 20%;">Buyer</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 25%;">Date & Time</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50; width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['property_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('h:i A', strtotime($booking['viewing_time'])); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="booking-details.php?id=<?php echo $booking['booking_id']; ?>" style="color: #3498DB; text-decoration: none;" data-tooltip="View booking details">View Details</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem; animation: bounce 2s ease-in-out infinite;">📅</div>
                    <p style="font-size: 1.2rem;">No bookings yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Animated Landlord Waving -->
    <div class="cartoon-character">
        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
            <!-- Landlord -->
            <g>
                <!-- Head -->
                <circle cx="100" cy="60" r="25" fill="#F4A460"/>
                <!-- Eyes -->
                <circle cx="92" cy="55" r="3" fill="#2C3E50"/>
                <circle cx="108" cy="55" r="3" fill="#2C3E50"/>
                <!-- Smile -->
                <path d="M 90 65 Q 100 70 110 65" stroke="#2C3E50" stroke-width="2" fill="none"/>
                <!-- Body -->
                <rect x="80" y="85" width="40" height="50" rx="5" fill="#3498DB"/>
                <!-- Legs -->
                <rect x="85" y="135" width="12" height="35" fill="#2C3E50"/>
                <rect x="103" y="135" width="12" height="35" fill="#2C3E50"/>
                <!-- Waving Arm (Right) -->
                <g class="landlord-wave">
                    <rect x="120" y="90" width="12" height="30" fill="#F4A460" transform-origin="126 90"/>
                    <circle cx="126" cy="122" r="8" fill="#F4A460"/>
                </g>
                <!-- Other Arm (Left) -->
                <rect x="68" y="90" width="12" height="30" fill="#F4A460"/>
                <!-- Hat -->
                <ellipse cx="100" cy="40" rx="30" ry="8" fill="#E74C3C"/>
                <rect x="85" y="35" width="30" height="10" fill="#E74C3C"/>
                <!-- Money symbols floating around -->
                <text x="140" y="70" font-size="20" class="money-float" style="animation-delay: 0s;">💰</text>
                <text x="50" y="90" font-size="20" class="money-float" style="animation-delay: 0.5s;">💵</text>
                <text x="130" y="120" font-size="20" class="money-float" style="animation-delay: 1s;">💴</text>
            </g>
        </svg>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Scroll reveal animation
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.scroll-reveal');
            reveals.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll(); // Initial check

        // Counter animation for stats
        function animateCounter(element) {
            const target = parseInt(element.innerText);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.innerText = target;
                    clearInterval(timer);
                } else {
                    element.innerText = Math.floor(current);
                }
            }, 16);
        }

        // Animate all stat numbers on page load
        window.addEventListener('load', () => {
            document.querySelectorAll('.stat-number').forEach(animateCounter);
        });
    </script>
</body>
</html>
