<?php
require_once 'includes/config.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Fetch statistics
$stats = [
    'total_properties' => $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc()['count'],
    'total_owners' => $conn->query("SELECT COUNT(*) as count FROM owners")->fetch_assoc()['count'],
    'total_buyers' => $conn->query("SELECT COUNT(*) as count FROM buyers")->fetch_assoc()['count'],
    'total_bookings' => $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'],
    'pending_bookings' => $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'")->fetch_assoc()['count'],
    'available_properties' => $conn->query("SELECT COUNT(*) as count FROM properties WHERE status = 'Available'")->fetch_assoc()['count']
];

// Recent activity
$recent_properties = $conn->query("SELECT p.*, o.name as owner_name FROM properties p JOIN owners o ON p.owner_id = o.owner_id ORDER BY p.created_at DESC LIMIT 5");
$recent_bookings = $conn->query("SELECT b.*, p.property_name, buy.fname, buy.lname, o.name as owner_name FROM bookings b JOIN properties p ON b.property_id = p.property_id JOIN buyers buy ON b.buyer_id = buy.buyer_id JOIN owners o ON p.owner_id = o.owner_id ORDER BY b.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }
        .admin-sidebar {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .admin-menu {
            list-style: none;
        }
        .admin-menu li {
            margin-bottom: 1rem;
        }
        .admin-menu a {
            display: block;
            padding: 0.8rem 1rem;
            color: #2C3E50;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .admin-menu a:hover,
        .admin-menu a.active {
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
        .data-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #2C3E50;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #E0E0E0;
        }
        .data-table tr:hover {
            background: #f8f9fa;
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
                <li><a href="admin-dashboard.php">Admin Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Admin Dashboard 🔐</h1>
        <p style="color: #7F8C8D; margin-bottom: 2rem;">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</p>

        <div class="admin-grid">
            <!-- Sidebar -->
            <div class="admin-sidebar">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #E74C3C, #C0392B); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        👨‍💼
                    </div>
                    <h3 style="color: #2C3E50; margin-bottom: 0.3rem;"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></h3>
                    <p style="color: #7F8C8D; font-size: 0.9rem;">Administrator</p>
                </div>
                
                <ul class="admin-menu">
                    <li><a href="#overview" class="active" onclick="showSection('overview')">📊 Overview</a></li>
                    <li><a href="#properties" onclick="showSection('properties')">🏘️ Properties</a></li>
                    <li><a href="#owners" onclick="showSection('owners')">👥 Owners</a></li>
                    <li><a href="#buyers" onclick="showSection('buyers')">🛒 Buyers</a></li>
                    <li><a href="#bookings" onclick="showSection('bookings')">📅 Bookings</a></li>
                    <li><a href="logout.php" style="color: #E74C3C;">🚪 Logout</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div>
                <!-- Overview Section -->
                <div id="overview-section">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">System Overview</h2>
                    
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
                            <div class="stat-number" style="color: #9B59B6;"><?php echo $stats['total_owners']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Property Owners</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #E67E22;"><?php echo $stats['total_buyers']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Registered Buyers</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #E74C3C;"><?php echo $stats['total_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Total Bookings</p>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="color: #F39C12;"><?php echo $stats['pending_bookings']; ?></div>
                            <p style="color: #7F8C8D; font-weight: 600;">Pending Bookings</p>
                        </div>
                    </div>

                    <h3 style="color: #2C3E50; margin-bottom: 1rem;">Recent Properties</h3>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Owner</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($property = $recent_properties->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($property['property_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($property['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($property['city']); ?></td>
                                        <td style="color: #E74C3C; font-weight: bold;">KES <?php echo number_format($property['price'], 2); ?></td>
                                        <td><span class="property-status status-<?php echo strtolower($property['status']); ?>"><?php echo $property['status']; ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($property['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <h3 style="color: #2C3E50; margin-bottom: 1rem;">Recent Bookings</h3>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Buyer</th>
                                    <th>Owner</th>
                                    <th>Viewing Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = $recent_bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($booking['property_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['owner_name']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['viewing_date'])); ?></td>
                                        <td><span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Properties Section -->
                <div id="properties-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">All Properties</h2>
                    <?php
                    $all_properties = $conn->query("SELECT p.*, o.name as owner_name FROM properties p JOIN owners o ON p.owner_id = o.owner_id ORDER BY p.created_at DESC");
                    ?>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property</th>
                                    <th>Type</th>
                                    <th>Owner</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($property = $all_properties->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $property['property_id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($property['property_name']); ?></strong></td>
                                        <td><?php echo $property['property_type']; ?></td>
                                        <td><?php echo htmlspecialchars($property['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($property['city']); ?></td>
                                        <td style="color: #E74C3C;">KES <?php echo number_format($property['price'], 2); ?></td>
                                        <td><span class="property-status status-<?php echo strtolower($property['status']); ?>"><?php echo $property['status']; ?></span></td>
                                        <td>
                                            <a href="property-details.php?id=<?php echo $property['property_id']; ?>" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.9rem;">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Owners Section -->
                <div id="owners-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Property Owners</h2>
                    <?php
                    $all_owners = $conn->query("SELECT * FROM owners ORDER BY created_at DESC");
                    ?>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Properties</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($owner = $all_owners->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $owner['owner_id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($owner['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($owner['email']); ?></td>
                                        <td><?php echo htmlspecialchars($owner['mobile_no']); ?></td>
                                        <td><?php echo $owner['no_of_houses']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($owner['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Buyers Section -->
                <div id="buyers-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">Registered Buyers</h2>
                    <?php
                    $all_buyers = $conn->query("SELECT * FROM buyers ORDER BY created_at DESC");
                    ?>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($buyer = $all_buyers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $buyer['buyer_id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($buyer['fname'] . ' ' . $buyer['lname']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($buyer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($buyer['mobile_no']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($buyer['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div id="bookings-section" style="display: none;">
                    <h2 style="color: #2C3E50; margin-bottom: 1.5rem;">All Bookings</h2>
                    <?php
                    $all_bookings = $conn->query("SELECT b.*, p.property_name, buy.fname, buy.lname, o.name as owner_name FROM bookings b JOIN properties p ON b.property_id = p.property_id JOIN buyers buy ON b.buyer_id = buy.buyer_id JOIN owners o ON p.owner_id = o.owner_id ORDER BY b.created_at DESC");
                    ?>
                    <div class="data-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property</th>
                                    <th>Buyer</th>
                                    <th>Owner</th>
                                    <th>Viewing Date</th>
                                    <th>Status</th>
                                    <th>Booked On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = $all_bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $booking['booking_id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($booking['property_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($booking['fname'] . ' ' . $booking['lname']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['owner_name']); ?></td>
                                        <td><?php echo date('M d, Y g:i A', strtotime($booking['viewing_date'] . ' ' . $booking['viewing_time'])); ?></td>
                                        <td><span class="status-badge status-<?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($booking['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
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
            document.getElementById('owners-section').style.display = 'none';
            document.getElementById('buyers-section').style.display = 'none';
            document.getElementById('bookings-section').style.display = 'none';
            
            document.getElementById(section + '-section').style.display = 'block';
            
            document.querySelectorAll('.admin-menu a').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
