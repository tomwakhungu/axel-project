<?php
require_once 'includes/config.php';

// Check if buyer is logged in
if(!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];

// Get buyer information
$buyer_query = "SELECT * FROM buyers WHERE buyer_id = $buyer_id";
$buyer_result = $conn->query($buyer_query);
$buyer = $buyer_result->fetch_assoc();

// Get buyer's bookings
$bookings_query = "SELECT b.*, p.property_name, p.location, p.city, p.price, o.name as owner_name, o.email as owner_email, o.mobile_no as owner_phone
                   FROM bookings b
                   JOIN properties p ON b.property_id = p.property_id
                   JOIN owners o ON p.owner_id = o.owner_id
                   WHERE b.buyer_id = $buyer_id
                   ORDER BY b.created_at DESC";
$bookings = $conn->query($bookings_query);

// Get count stats
$total_bookings = $bookings ? $bookings->num_rows : 0;
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE buyer_id = $buyer_id AND status = 'Pending'")->fetch_assoc()['count'];
$confirmed_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE buyer_id = $buyer_id AND status = 'Confirmed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Floating Background -->
    <div class="floating-bg">
        <div class="floating-icon">🏠</div>
        <div class="floating-icon">🔑</div>
        <div class="floating-icon">🏡</div>
        <div class="floating-icon">🌟</div>
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
                <li><a href="buyer-dashboard.php" class="active">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="margin-bottom: 2rem; animation: fadeInDown 0.6s ease-out;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($buyer['fname']); ?>! 👋</h1>
            <p style="color: #7F8C8D;">Find your dream home in Nairobi</p>
        </div>

        <!-- Dashboard Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">📅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $total_bookings; ?></div>
                <div style="opacity: 0.9;">Total Bookings</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">⏳</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $pending_bookings; ?></div>
                <div style="opacity: 0.9;">Pending Viewings</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $confirmed_bookings; ?></div>
                <div style="opacity: 0.9;">Confirmed Viewings</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">🚀 Quick Actions</h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="properties.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;" data-tooltip="Browse available properties">
                    <span style="font-size: 1.2rem;">🏘️</span> Browse Properties
                </a>
                <a href="my-bookings.php" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #34495E; color: white;" data-tooltip="View your bookings">
                    <span style="font-size: 1.2rem;">📋</span> My Bookings
                </a>
                <a href="buyer-profile.php" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #16A085; color: white;" data-tooltip="Update your profile">
                    <span style="font-size: 1.2rem;">👤</span> My Profile
                </a>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">📅 My Property Viewings</h2>
            
            <?php if($bookings && $bookings->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #E74C3C;">
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Property</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Location</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Date & Time</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #2C3E50;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $bookings->data_seek(0); // Reset pointer
                            while($booking = $bookings->fetch_assoc()): 
                            ?>
                            <tr style="border-bottom: 1px solid #E0E0E0;">
                                <td style="padding: 1rem;">
                                    <strong><?php echo htmlspecialchars($booking['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;">KES <?php echo number_format($booking['price'], 2); ?></small>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($booking['location']); ?>, <?php echo htmlspecialchars($booking['city']); ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?><br>
                                    <small style="color: #7F8C8D;"><?php echo date('h:i A', strtotime($booking['viewing_time'])); ?></small>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="booking-status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="property-details.php?id=<?php echo $booking['property_id']; ?>" style="color: #3498DB; text-decoration: none; margin-right: 1rem;" data-tooltip="View property">View Property</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7F8C8D;">
                    <div style="font-size: 4rem; margin-bottom: 1rem; animation: bounce 2s ease-in-out infinite;">🏠</div>
                    <p style="font-size: 1.2rem; margin-bottom: 1rem;">No bookings yet</p>
                    <p style="margin-bottom: 2rem;">Start exploring properties and book viewings!</p>
                    <a href="properties.php" class="btn btn-primary">Browse Properties</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Animated House with Opening Door -->
    <div class="cartoon-character">
        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
            <!-- House -->
            <g>
                <!-- Roof -->
                <polygon points="100,40 60,80 140,80" fill="#E74C3C"/>
                <!-- Main House Body -->
                <rect x="70" y="80" width="60" height="70" fill="#ECF0F1"/>
                <!-- Window Left -->
                <rect x="80" y="95" width="15" height="15" fill="#3498DB"/>
                <line x1="87.5" y1="95" x2="87.5" y2="110" stroke="white" stroke-width="1"/>
                <line x1="80" y1="102.5" x2="95" y2="102.5" stroke="white" stroke-width="1"/>
                <!-- Window Right -->
                <rect x="105" y="95" width="15" height="15" fill="#3498DB"/>
                <line x1="112.5" y1="95" x2="112.5" y2="110" stroke="white" stroke-width="1"/>
                <line x1="105" y1="102.5" x2="120" y2="102.5" stroke="white" stroke-width="1"/>
                <!-- Door Frame -->
                <rect x="92" y="120" width="16" height="30" fill="#95A5A6"/>
                <!-- Door (Opening) -->
                <rect x="92" y="120" width="16" height="30" fill="#7F8C8D" class="house-door"/>
                <!-- Door Knob -->
                <circle cx="105" cy="135" r="2" fill="#F39C12"/>
                <!-- Welcome Mat -->
                <rect x="88" y="150" width="24" height="3" fill="#C0392B"/>
                <!-- Chimney -->
                <rect x="115" y="55" width="10" height="25" fill="#95A5A6"/>
                <!-- Smoke -->
                <circle cx="120" cy="45" r="3" fill="#BDC3C7" class="money-float" style="animation-delay: 0s;"/>
                <circle cx="122" cy="38" r="4" fill="#BDC3C7" class="money-float" style="animation-delay: 0.3s;"/>
                <circle cx="118" cy="32" r="3" fill="#BDC3C7" class="money-float" style="animation-delay: 0.6s;"/>
                <!-- Keys floating -->
                <text x="145" y="100" font-size="18" class="keys-swing">🔑</text>
                <!-- Heart -->
                <text x="50" y="100" font-size="18" class="wiggle-animation">❤️</text>
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
        revealOnScroll();

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

        window.addEventListener('load', () => {
            document.querySelectorAll('.stat-number').forEach(animateCounter);
        });
    </script>
</body>
</html>
