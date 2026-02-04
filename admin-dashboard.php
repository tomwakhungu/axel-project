<?php
require_once 'includes/config.php';

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$total_properties = $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc()['count'];
$total_owners = $conn->query("SELECT COUNT(*) as count FROM owners")->fetch_assoc()['count'];
$total_buyers = $conn->query("SELECT COUNT(*) as count FROM buyers")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$available_properties = $conn->query("SELECT COUNT(*) as count FROM properties WHERE status = 'Available'")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'")->fetch_assoc()['count'];

// Get recent properties
$recent_properties = $conn->query("SELECT p.*, o.name as owner_name FROM properties p JOIN owners o ON p.owner_id = o.owner_id ORDER BY p.created_at DESC LIMIT 5");

// Get recent bookings
$recent_bookings = $conn->query("SELECT b.*, p.property_name, bu.fname, bu.lname, o.name as owner_name 
                                 FROM bookings b 
                                 JOIN properties p ON b.property_id = p.property_id 
                                 JOIN buyers bu ON b.buyer_id = bu.buyer_id 
                                 JOIN owners o ON p.owner_id = o.owner_id 
                                 ORDER BY b.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Floating Background -->
    <div class="floating-bg">
        <div class="floating-icon">📊</div>
        <div class="floating-icon">💼</div>
        <div class="floating-icon">📈</div>
        <div class="floating-icon">🏢</div>
    </div>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-icon">🏠</span>
                <span><?php echo SITE_NAME; ?></span>
            </a>
            <ul class="nav-menu">
                <li><a href="admin-dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage-properties.php">Properties</a></li>
                <li><a href="manage-owners.php">Owners</a></li>
                <li><a href="manage-buyers.php">Buyers</a></li>
                <li><a href="manage-bookings.php">Bookings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <div style="margin-bottom: 2rem; animation: fadeInDown 0.6s ease-out;">
            <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Admin Dashboard 👨‍💼</h1>
            <p style="color: #7F8C8D;">Manage your real estate platform</p>
        </div>

        <!-- Dashboard Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">🏠</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $total_properties; ?></div>
                <div style="opacity: 0.9;">Total Properties</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">👥</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $total_owners; ?></div>
                <div style="opacity: 0.9;">Property Owners</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">🛒</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $total_buyers; ?></div>
                <div style="opacity: 0.9;">Buyers</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">📅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $total_bookings; ?></div>
                <div style="opacity: 0.9;">Total Bookings</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #1ABC9C 0%, #16A085 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">✅</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $available_properties; ?></div>
                <div style="opacity: 0.9;">Available Properties</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #F39C12 0%, #E67E22 100%); padding: 2rem; border-radius: 12px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="stat-icon" style="font-size: 3rem; margin-bottom: 0.5rem;">⏳</div>
                <div class="stat-number" style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;"><?php echo $pending_bookings; ?></div>
                <div style="opacity: 0.9;">Pending Bookings</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">⚡ Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <a href="manage-properties.php" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;" data-tooltip="Manage all properties">
                    <span style="font-size: 1.2rem;">🏠</span> Manage Properties
                </a>
                <a href="manage-owners.php" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #E74C3C; color: white;" data-tooltip="Manage property owners">
                    <span style="font-size: 1.2rem;">👥</span> Manage Owners
                </a>
                <a href="manage-buyers.php" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #9B59B6; color: white;" data-tooltip="Manage buyers">
                    <span style="font-size: 1.2rem;">🛒</span> Manage Buyers
                </a>
                <a href="manage-bookings.php" class="btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #27AE60; color: white;" data-tooltip="Manage bookings">
                    <span style="font-size: 1.2rem;">📅</span> Manage Bookings
                </a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Recent Properties -->
            <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">🏘️ Recent Properties</h2>
                
                <?php if($recent_properties && $recent_properties->num_rows > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php while($property = $recent_properties->fetch_assoc()): ?>
                        <div style="padding: 1rem; border: 1px solid #E0E0E0; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.transform='translateX(5px)';" onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <strong style="color: #2C3E50;"><?php echo htmlspecialchars($property['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;"><?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?></small><br>
                                    <small style="color: #E74C3C; font-weight: 600;">KES <?php echo number_format($property['price'], 2); ?></small>
                                </div>
                                <span class="property-status status-<?php echo strtolower($property['status']); ?>" style="font-size: 0.75rem;">
                                    <?php echo $property['status']; ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #7F8C8D; padding: 2rem;">No properties yet</p>
                <?php endif; ?>
            </div>

            <!-- Recent Bookings -->
            <div class="scroll-reveal" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">📅 Recent Bookings</h2>
                
                <?php if($recent_bookings && $recent_bookings->num_rows > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                        <div style="padding: 1rem; border: 1px solid #E0E0E0; border-radius: 8px; transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.transform='translateX(5px)';" onmouseout="this.style.boxShadow='none'; this.style.transform='translateX(0)';">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <strong style="color: #2C3E50;"><?php echo htmlspecialchars($booking['property_name']); ?></strong><br>
                                    <small style="color: #7F8C8D;">Buyer: <?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></small><br>
                                    <small style="color: #7F8C8D;"><?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('h:i A', strtotime($booking['viewing_time'])); ?></small>
                                </div>
                                <span class="booking-status status-<?php echo strtolower($booking['status']); ?>" style="font-size: 0.75rem;">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #7F8C8D; padding: 2rem;">No bookings yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Animated Admin Character -->
    <div class="cartoon-character">
        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
            <!-- Admin with clipboard and charts -->
            <g>
                <!-- Clipboard -->
                <rect x="85" y="100" width="30" height="40" rx="3" fill="#ECF0F1" stroke="#95A5A6" stroke-width="2"/>
                <rect x="90" y="105" width="20" height="3" fill="#3498DB"/>
                <rect x="90" y="112" width="15" height="2" fill="#7F8C8D"/>
                <rect x="90" y="117" width="18" height="2" fill="#7F8C8D"/>
                <rect x="90" y="122" width="12" height="2" fill="#7F8C8D"/>
                <!-- Pen -->
                <rect x="112" y="115" width="3" height="15" fill="#E74C3C" class="wiggle-animation"/>
                <!-- Head -->
                <circle cx="100" cy="60" r="20" fill="#F4A460"/>
                <!-- Glasses -->
                <circle cx="92" cy="58" r="6" fill="none" stroke="#2C3E50" stroke-width="2"/>
                <circle cx="108" cy="58" r="6" fill="none" stroke="#2C3E50" stroke-width="2"/>
                <line x1="98" y1="58" x2="102" y2="58" stroke="#2C3E50" stroke-width="2"/>
                <!-- Smile -->
                <path d="M 92 66 Q 100 70 108 66" stroke="#2C3E50" stroke-width="2" fill="none"/>
                <!-- Body -->
                <rect x="85" y="80" width="30" height="20" rx="3" fill="#2C3E50"/>
                <!-- Tie -->
                <polygon points="100,80 95,90 100,95 105,90" fill="#E74C3C"/>
                <!-- Chart floating -->
                <g class="money-float">
                    <rect x="130" y="70" width="25" height="20" fill="white" stroke="#E74C3C" stroke-width="1"/>
                    <polyline points="135,85 140,80 145,83 150,78" stroke="#27AE60" stroke-width="2" fill="none"/>
                </g>
                <!-- Dollar signs -->
                <text x="65" y="90" font-size="16" class="money-float" style="animation-delay: 0.5s;">💰</text>
                <text x="135" y="110" font-size="16" class="money-float" style="animation-delay: 1s;">📊</text>
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
