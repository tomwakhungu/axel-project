<?php
require_once 'includes/config.php';

if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];

// Fetch owner details
$owner_query = "SELECT * FROM owners WHERE owner_id = $owner_id";
$owner = $conn->query($owner_query)->fetch_assoc();

// Update house count
$update_count = "UPDATE owners SET no_of_houses = (SELECT COUNT(*) FROM properties WHERE owner_id = $owner_id) WHERE owner_id = $owner_id";
$conn->query($update_count);

// Fetch properties
$properties_query = "SELECT * FROM properties WHERE owner_id = $owner_id ORDER BY created_at DESC";
$properties_result = $conn->query($properties_query);

// Fetch bookings for owner's properties
$bookings_query = "SELECT b.*, p.property_name, p.location, p.city, buy.fname, buy.lname, buy.email, buy.mobile_no
                   FROM bookings b
                   JOIN properties p ON b.property_id = p.property_id
                   JOIN buyers buy ON b.buyer_id = buy.buyer_id
                   WHERE p.owner_id = $owner_id
                   ORDER BY b.created_at DESC";
$bookings_result = $conn->query($bookings_query);

// Statistics
$stats_query = "SELECT 
                (SELECT COUNT(*) FROM properties WHERE owner_id = $owner_id) as total_properties,
                (SELECT COUNT(*) FROM properties WHERE owner_id = $owner_id AND status = 'Available') as available_properties,
                (SELECT COUNT(*) FROM properties WHERE owner_id = $owner_id AND status = 'Sold') as sold_properties,
                (SELECT COUNT(*) FROM bookings b JOIN properties p ON b.property_id = p.property_id WHERE p.owner_id = $owner_id AND b.status = 'Pending') as pending_bookings";
$stats = $conn->query($stats_query)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard - <?php echo SITE_NAME; ?></title>
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
            background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
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
        .property-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .property-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .property-table th {
            background: #2C3E50;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        .property-table td {
            padding: 1rem;
            border-bottom: 1px solid #E0E0E0;
        }
        .property-table tr:hover {
            background: #f8f9fa;
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            display: inline-block;
        }
        .booking-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
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
            .property-table {
                overflow-x: auto;
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
                <li><a href="owner-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($owner['name']); ?>! 👋</h1>
        <p style="color: #7F8C8D; margin-bottom: 2rem;">Manage your properties and bookings</p>

        <div class="dashboard-grid">
            <!-- Sidebar -->
            <div class="sidebar">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #E74C3C, #C0392B); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <?php echo strtoupper(substr($owner['name'], 0, 1)); ?>
                    </div>
                    <h3 style="color: #2C3E50; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($owner['name']); ?></h3>
                    <p style="color: #7F8C8D; font-size: 0.9rem;">Property Owner</p>
                </div>
                
                <ul class="sidebar-menu">
                    <li><a href="#overview" class="active" onclick="showSection('overview')">📊 Overview</a></li>
                    <li><a href="#properties" onclick="showSection('properties')">🏘️ My Properties</a></li>
                    <li><a href="#bookings" onclick="showSection('bookings')">📅 Bookings</a></li>
                    <li><a href="#profile" onclick="showSection('profile')">👤 Profile</a></li>
                    <li><a href="add-property.php" style="background: #27AE60; color: white;">➕ Add Property</a></li>
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
                            <div class="stat-number" style="color: #3498DB;"><?php echo $stats['total_properties']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Total Properties</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #27AE60;"><?php echo $stats['available_properties']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Available</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #E74C3C;"><?php echo $stats['sold_properties']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Sold</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #F39C12;"><?php echo $stats['pending_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Pending Bookings</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                        <a href="add-property.php" style="background: linear-gradient(135deg, #27AE60 0%, #229954 100%); color: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">➕</div>
                            <h3 style="margin: 0;">Add New Property</h3>
                        </a>
                        <a href="#properties" onclick="showSection('properties')" style="background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%); color: white; padding: 2rem; border-radius: 8px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🏘️</div>
                            <h3 style="margin: 0;">View All Properties</h3>
                        </a>
                    </div>

                    <h3 style="color: #2C3E50; margin-bottom: 1rem;">Recent Bookings</h3>
                    <?php 
                    $recent_bookings = $conn->query("SELECT b.*, p.property_name, buy.fname, buy.lname 
                                                      FROM bookings b
                                                      JOIN properties p ON b.property_id = p.property_id
                                                      JOIN buyers buy ON b.buyer_id = buy.buyer_id
                                                      WHERE p.owner_id = $owner_id
                                                      ORDER BY b.created_at DESC LIMIT 5");
                    if($recent_bookings->num_rows > 0):
                        while($booking = $recent_bookings->fetch_assoc()):
                    ?>
                        <div class="booking-card">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <h4 style="color: #2C3E50; margin: 0;"><?php echo htmlspecialchars($booking['property_name']); ?></h4>
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </div>
                            <p style="color: #7F8C8D; margin: 0.3rem 0;">👤 Buyer: <?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></p>
                            <p style="color: #7F8C8D; margin: 0.3rem 0;">📅 Viewing: <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('g:i A', strtotime($booking['viewing_time'])); ?></p>
                        </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <p style="text-align: center; padding: 2rem; color: #7F8C8D; background: white; border-radius: 8px;">No bookings yet</p>
                    <?php endif; ?>
                </div>

                <!-- Properties Section -->
                <div id="properties-section" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="color: #2C3E50; margin: 0;">My Properties</h2>
                        <a href="add-property.php" class="btn btn-primary">➕ Add Property</a>
                    </div>
                    
                    <?php if($properties_result->num_rows > 0): ?>
                        <div class="property-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th>Location</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($property = $properties_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong style="color: #2C3E50;"><?php echo htmlspecialchars($property['property_name']); ?></strong>
                                            </td>
                                            <td style="color: #7F8C8D;"><?php echo htmlspecialchars($property['city']); ?></td>
                                            <td style="color: #E74C3C; font-weight: bold;">KES <?php echo number_format($property['price'], 2); ?></td>
                                            <td style="color: #7F8C8D;"><?php echo $property['property_type']; ?></td>
                                            <td>
                                                <span class="property-status status-<?php echo strtolower($property['status']); ?>">
                                                    <?php echo $property['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="property-details.php?id=<?php echo $property['property_id']; ?>" class="action-btn btn-primary" title="View">👁️</a>
                                                <a href="edit-property.php?id=<?php echo $property['property_id']; ?>" class="action-btn btn-secondary" title="Edit">✏️</a>
                                                <a href="delete-property.php?id=<?php echo $property['property_id']; ?>" class="action-btn btn-danger" title="Delete" onclick="return confirm('Are you sure?')">🗑️</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 8px;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">🏘️</div>
                            <h3 style="color: #7F8C8D; margin-bottom: 1rem;">No Properties Yet</h3>
                            <p style="color: #95A5A6; margin-bottom: 2rem;">Start by adding your first property!</p>
                            <a href="add-property.php" class="btn btn-primary" style="display: inline-block; padding: 1rem 2rem;">➕ Add Property</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Bookings Section -->
                <div id="bookings-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Property Viewing Bookings</h2>
                    
                    <?php if($bookings_result->num_rows > 0): ?>
                        <?php while($booking = $bookings_result->fetch_assoc()): ?>
                            <div class="booking-card">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="color: #2C3E50; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($booking['property_name']); ?></h3>
                                        <p style="color: #7F8C8D; margin: 0;">📍 <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?></p>
                                    </div>
                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </div>
                                
                                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                    <p style="color: #2C3E50; font-weight: bold; margin-bottom: 0.5rem;">Buyer Information:</p>
                                    <p style="color: #7F8C8D; margin: 0.3rem 0;">👤 Name: <?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></p>
                                    <p style="color: #7F8C8D; margin: 0.3rem 0;">📧 Email: <?php echo htmlspecialchars($booking['email']); ?></p>
                                    <p style="color: #7F8C8D; margin: 0.3rem 0;">📱 Phone: <?php echo htmlspecialchars($booking['mobile_no']); ?></p>
                                </div>
                                
                                <p style="color: #7F8C8D; margin: 0.5rem 0;">📅 <strong>Viewing Date:</strong> <?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?> at <?php echo date('g:i A', strtotime($booking['viewing_time'])); ?></p>
                                <?php if($booking['message']): ?>
                                    <p style="color: #7F8C8D; margin: 0.5rem 0;">💬 <strong>Message:</strong> <?php echo htmlspecialchars($booking['message']); ?></p>
                                <?php endif; ?>
                                <p style="color: #95A5A6; font-size: 0.9rem; margin-top: 0.5rem;">Booked on: <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
                                
                                <?php if($booking['status'] == 'Pending'): ?>
                                    <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                                        <a href="confirm-booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem;">✓ Confirm</a>
                                        <a href="cancel-booking.php?id=<?php echo $booking['booking_id']; ?>" class="btn btn-danger" style="padding: 0.5rem 1rem;" onclick="return confirm('Are you sure?')">✗ Cancel</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 8px;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">📅</div>
                            <h3 style="color: #7F8C8D; margin-bottom: 1rem;">No Bookings Yet</h3>
                            <p style="color: #95A5A6;">Bookings will appear here when buyers schedule viewings</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Profile Section -->
                <div id="profile-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">My Profile</h2>
                    
                    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Full Name:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($owner['name']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Email:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($owner['email']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Mobile:</label>
                                <p style="color: #7F8C8D;"><?php echo htmlspecialchars($owner['mobile_no']); ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Total Properties:</label>
                                <p style="color: #7F8C8D;"><?php echo $owner['no_of_houses']; ?></p>
                            </div>
                            <div>
                                <label style="font-weight: bold; color: #2C3E50; display: block; margin-bottom: 0.5rem;">Member Since:</label>
                                <p style="color: #7F8C8D;"><?php echo date('M d, Y', strtotime($owner['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E0E0E0;">
                            <a href="edit-owner-profile.php" class="btn btn-primary" style="display: inline-block; padding: 0.8rem 2rem;">Edit Profile</a>
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
            document.getElementById('overview-section').style.display = 'none';
            document.getElementById('properties-section').style.display = 'none';
            document.getElementById('bookings-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
            
            document.getElementById(section + '-section').style.display = 'block';
            
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
