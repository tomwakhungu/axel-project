<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type'];
    
    if(empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        if($user_type == 'buyer') {
            $query = "SELECT * FROM buyers WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result->num_rows == 1) {
                $buyer = $result->fetch_assoc();
                if(password_verify($password, $buyer['pwd'])) {
                    $_SESSION['buyer_id'] = $buyer['buyer_id'];
                    $_SESSION['buyer_name'] = $buyer['fname'] . ' ' . $buyer['lname'];
                    $_SESSION['user_type'] = 'buyer';
                    header("Location: buyer-dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "No account found with this email";
            }
        } elseif($user_type == 'owner') {
            $query = "SELECT * FROM owners WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result->num_rows == 1) {
                $owner = $result->fetch_assoc();
                if(password_verify($password, $owner['pwd'])) {
                    $_SESSION['owner_id'] = $owner['owner_id'];
                    $_SESSION['owner_name'] = $owner['name'];
                    $_SESSION['user_type'] = 'owner';
                    header("Location: owner-dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "No account found with this email";
            }
        } elseif($user_type == 'admin') {
            $query = "SELECT * FROM admins WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result->num_rows == 1) {
                $admin = $result->fetch_assoc();
                if(password_verify($password, $admin['pwd'])) {
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['user_type'] = 'admin';
                    header("Location: admin-dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "Invalid admin credentials";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
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
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center" style="margin-bottom: 2rem; color: #2C3E50;">Login to Your Account</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="user_type">I am a:</label>
                    <select name="user_type" id="user_type" required>
                        <option value="buyer">Property Buyer</option>
                        <option value="owner">Property Owner</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    Login
                </button>
            </form>
            
            <p class="text-center mt-2">
                Don't have an account? <a href="register.php" style="color: #E74C3C; font-weight: bold;">Register here</a>
            </p>
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
