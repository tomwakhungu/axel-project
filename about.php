<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .about-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495E 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .about-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .about-section {
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.2rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 2px;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .mission-vision-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid var(--secondary-color);
        }
        
        .mission-vision-card h3 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .mission-vision-card p {
            color: #666;
            line-height: 1.8;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .value-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .value-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .value-card h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .value-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .team-member {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .team-member-avatar {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }
        
        .team-member-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .team-member-info h4 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .team-member-info p {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .team-member-info small {
            color: #999;
            display: block;
        }
        
        .stats-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495E 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 8px;
            margin-bottom: 3rem;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-item p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .about-hero h1 {
                font-size: 2rem;
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
                <li><a href="about.php" style="border-bottom: 2px solid var(--secondary-color);">About</a></li>
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
    <div class="about-hero">
        <h1>About <?php echo SITE_NAME; ?></h1>
        <p>Your Trusted Partner in Real Estate Since 2020</p>
    </div>

    <div class="container">
        <!-- Mission & Vision Section -->
        <div class="about-section">
            <h2 class="section-title">Our Mission & Vision</h2>
            <div class="mission-vision">
                <div class="mission-vision-card">
                    <h3>🎯 Our Mission</h3>
                    <p>
                        To revolutionize the real estate industry in Kenya by providing a transparent, 
                        user-friendly platform that connects property owners with qualified buyers and renters. 
                        We are committed to making property transactions seamless, secure, and accessible to everyone.
                    </p>
                </div>
                <div class="mission-vision-card">
                    <h3>👁️ Our Vision</h3>
                    <p>
                        To become East Africa's leading real estate marketplace, empowering individuals to find 
                        their dream properties and helping property owners maximize their investment returns. 
                        We envision a future where real estate transactions are efficient and transparent.
                    </p>
                </div>
            </div>
        </div>

        <!-- Core Values Section -->
        <div class="about-section">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h4>Integrity</h4>
                    <p>
                        We believe in honesty and transparency in all our dealings. 
                        Trust is the foundation of our relationships with clients.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">⭐</div>
                    <h4>Excellence</h4>
                    <p>
                        We strive for the highest standards in service delivery and 
                        continuously improve our platform and services.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🔒</div>
                    <h4>Security</h4>
                    <p>
                        We prioritize data security and privacy, protecting our users' 
                        information with the latest technology.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌟</div>
                    <h4>Innovation</h4>
                    <p>
                        We embrace technology and innovation to provide cutting-edge 
                        solutions for modern real estate challenges.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">👥</div>
                    <h4>Customer Focus</h4>
                    <p>
                        Your satisfaction is our priority. We listen to feedback and 
                        continuously adapt to meet your needs.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">📈</div>
                    <h4>Growth</h4>
                    <p>
                        We believe in mutual growth and success for all stakeholders in 
                        our real estate ecosystem.
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <h2 class="section-title" style="color: white; margin-bottom: 3rem;">By The Numbers</h2>
            <div class="stats-container">
                <div class="stat-item">
                    <h3>5000+</h3>
                    <p>Properties Listed</p>
                </div>
                <div class="stat-item">
                    <h3>50000+</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-item">
                    <h3>2000+</h3>
                    <p>Completed Transactions</p>
                </div>
                <div class="stat-item">
                    <h3>95%</h3>
                    <p>Customer Satisfaction</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="about-section">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-member-avatar">🧑‍💼</div>
                    <div class="team-member-info">
                        <h4>John Karangu</h4>
                        <p>Founder & CEO</p>
                        <small>Real Estate Entrepreneur</small>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-avatar">👩‍💼</div>
                    <div class="team-member-info">
                        <h4>Sarah Mwangi</h4>
                        <p>Operations Manager</p>
                        <small>Business Development</small>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-avatar">👨‍💻</div>
                    <div class="team-member-info">
                        <h4>Peter Kipchoge</h4>
                        <p>Technology Lead</p>
                        <small>Software Engineering</small>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-member-avatar">👩‍🔧</div>
                    <div class="team-member-info">
                        <h4>Grace Njeri</h4>
                        <p>Customer Support Manager</p>
                        <small>Client Relations</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="about-section">
            <h2 class="section-title">Why Choose Us?</h2>
            <div style="background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Verified Listings</h4>
                        <p style="color: #666;">All properties are verified to ensure authenticity and quality information.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Secure Transactions</h4>
                        <p style="color: #666;">Our platform uses industry-leading security protocols to protect all transactions.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Expert Support</h4>
                        <p style="color: #666;">Our team is available 24/7 to assist with any questions or concerns.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Competitive Pricing</h4>
                        <p style="color: #666;">Access a wide range of properties at competitive market prices.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Advanced Search</h4>
                        <p style="color: #666;">Filter properties by location, price, type, and features with ease.</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">✓ Mobile Friendly</h4>
                        <p style="color: #666;">Access the platform on any device - desktop, tablet, or smartphone.</p>
                    </div>
                </div>
            </div>
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
                <p><a href="contact.php">Contact</a></p>
            </div>
            <div class="footer-section">
                <h3>For Owners</h3>
                <p><a href="register.php?type=owner">List Your Property</a></p>
                <p><a href="login.php">Owner Login</a></p>
            </div>
            <div class="footer-section">
                <h3>For Buyers</h3>
                <p><a href="register.php?type=buyer">Create Account</a></p>
                <p><a href="login.php">Buyer Login</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
