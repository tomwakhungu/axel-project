<?php
require_once 'includes/config.php';

if(isset($_SESSION['admin_id'])) {
    header("Location: admin-dashboard.php");
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    
    if(empty($email) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        $query = "SELECT * FROM admins WHERE email = '$email'";
        $result = $conn->query($query);
        
        if($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if(password_verify($password, $admin['pwd'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['name'];
                header("Location: admin-dashboard.php");
                exit();
            } else {
                $error = "Invalid credentials";
            }
        } else {
            $error = "Invalid credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
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
                <li><a href="properties.php">Properties</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #E74C3C, #C0392B); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem;">
                        🔐
                    </div>
                    <h2 style="color: #2C3E50; margin-bottom: 0.5rem;">Admin Login</h2>
                    <p style="color: #7F8C8D;">Access the admin dashboard</p>
                </div>
                
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" required placeholder="admin@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Login to Admin Panel</button>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="index.php" style="color: #E74C3C; text-decoration: none;">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
