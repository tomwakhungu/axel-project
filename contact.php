<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $subject = $conn->real_escape_string(trim($_POST['subject']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    // Validate
    if(empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // In a real application, you would send this to a contact messages table
        // For now, we'll send an email notification
        $to = "info@realestateHub.com"; // Change to your email
        $email_subject = "New Contact Form Submission: $subject";
        $email_body = "
            Name: $name\n
            Email: $email\n
            Phone: $phone\n
            Subject: $subject\n\n
            Message:\n
            $message
        ";
        $headers = "From: $email";
        
        // Uncomment to send email (requires mail server configured)
        // mail($to, $email_subject, $email_body, $headers);
        
        // Store in session to show success message
        $success = "Thank you for contacting us! We will get back to you shortly.";
        $_POST = array(); // Clear form
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495E 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .contact-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .contact-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        
        .contact-form {
            background: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid var(--secondary-color);
        }
        
        .info-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .info-card p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 0.5rem;
        }
        
        .info-card a {
            color: var(--secondary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .info-card a:hover {
            text-decoration: underline;
        }
        
        .info-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .form-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
        }
        
        .map-container {
            background: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }
        
        .map-container h3 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .map-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #E8F4F8 0%, #F0F8FC 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7F8C8D;
            font-size: 1.2rem;
        }
        
        .faq-section {
            background: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 3rem;
        }
        
        .faq-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .faq-item {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #E0E0E0;
            padding-bottom: 1.5rem;
        }
        
        .faq-item:last-child {
            border-bottom: none;
        }
        
        .faq-question {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .faq-answer {
            color: #666;
            line-height: 1.6;
        }
        
        .social-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #E0E0E0;
        }
        
        .social-links h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .social-icons {
            display: flex;
            gap: 1rem;
        }
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .social-icon:hover {
            transform: translateY(-3px);
            background: #C0392B;
        }
        
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
            
            .contact-header h1 {
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
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php" style="border-bottom: 2px solid var(--secondary-color);">Contact</a></li>
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
    <div class="contact-header">
        <h1>Contact Us</h1>
        <p>We're here to help and answer any question you might have</p>
    </div>

    <div class="container">
        <!-- Contact Form and Info -->
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="contact-form">
                <h2 class="form-title">Send us a Message</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success">✓ <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required placeholder="Your full name"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required placeholder="your@email.com"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="0700123456"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required placeholder="How can we help?"
                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required placeholder="Please describe your inquiry in detail..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="contact-info">
                <!-- Address -->
                <div class="info-card">
                    <div class="info-icon">📍</div>
                    <h3>Our Address</h3>
                    <p>Real Estate Hub Ltd.</p>
                    <p>Westlands, Nairobi</p>
                    <p>Kenya</p>
                </div>

                <!-- Phone -->
                <div class="info-card">
                    <div class="info-icon">📞</div>
                    <h3>Phone Number</h3>
                    <p>Main: <a href="tel:+254712345678">+254 (0) 712 345 678</a></p>
                    <p>Support: <a href="tel:+254722345678">+254 (0) 722 345 678</a></p>
                    <p style="margin-top: 1rem; color: #999; font-size: 0.9rem;">Available Mon-Fri, 9AM - 6PM</p>
                </div>

                <!-- Email -->
                <div class="info-card">
                    <div class="info-icon">✉️</div>
                    <h3>Email Us</h3>
                    <p>General: <a href="mailto:info@realestateHub.com">info@realestateHub.com</a></p>
                    <p>Support: <a href="mailto:support@realestateHub.com">support@realestateHub.com</a></p>
                    <p>Inquiries: <a href="mailto:inquiries@realestateHub.com">inquiries@realestateHub.com</a></p>
                </div>

                <!-- Social Media -->
                <div class="info-card">
                    <div class="info-icon">🌐</div>
                    <h3>Follow Us</h3>
                    <p>Connect with us on social media for updates and news</p>
                    <div class="social-links">
                        <div class="social-icons">
                            <a href="#" class="social-icon" title="Facebook">f</a>
                            <a href="#" class="social-icon" title="Twitter">𝕏</a>
                            <a href="#" class="social-icon" title="Instagram">📷</a>
                            <a href="#" class="social-icon" title="LinkedIn">in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-container">
            <h3>🗺️ Visit Our Office</h3>
            <div class="map-placeholder">
                📍 Google Maps Integration Coming Soon - Westlands, Nairobi
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            
            <div class="faq-item">
                <div class="faq-question">❓ How can I list my property on <?php echo SITE_NAME; ?>?</div>
                <div class="faq-answer">
                    To list your property, simply register as a property owner on our platform. Once verified, 
                    you can add property details, upload images, and set the price. Our team will review and 
                    publish your listing.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ Are there any fees to list properties?</div>
                <div class="faq-answer">
                    We offer competitive pricing. For details on current listing fees and packages, 
                    please <a href="contact.php">contact our sales team</a>.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ How do I book a property viewing?</div>
                <div class="faq-answer">
                    As a registered buyer, you can browse properties and click "Book Viewing" on the property 
                    details page. The owner will receive your request and confirm the appointment time.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ Is my payment information secure?</div>
                <div class="faq-answer">
                    Yes, we use industry-leading encryption and security protocols to protect all transactions 
                    and personal information. Your data is never shared with third parties.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ How can I edit or delete my listing?</div>
                <div class="faq-answer">
                    Log into your owner dashboard and navigate to "My Properties". From there, you can edit 
                    property details, upload additional images, or mark properties as sold.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ What if I have an issue with a transaction?</div>
                <div class="faq-answer">
                    Our support team is available 24/7 to help resolve disputes. Contact us via email or phone 
                    with details of your issue, and we'll investigate and provide a resolution.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ Can I filter properties by specific criteria?</div>
                <div class="faq-answer">
                    Yes! Use our advanced search filters on the Properties page to find properties by location, 
                    price range, property type, and more.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">❓ How do I write a review for a property?</div>
                <div class="faq-answer">
                    After booking a viewing or purchasing a property, you can write a review from your buyer 
                    dashboard. Your honest feedback helps other users make informed decisions.
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
