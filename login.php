<?php
require_once 'includes/config.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type'];
    
    if(empty($email) || empty($password)) {
        $error = "Please fill all fields";
    } else {
        if($user_type == 'admin') {
            $query = "SELECT * FROM admins WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result && $result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                $stored_password = isset($admin['password']) ? $admin['password'] : (isset($admin['pwd']) ? $admin['pwd'] : '');
                
                if(password_verify($password, $stored_password)) {
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_name'] = isset($admin['username']) ? $admin['username'] : (isset($admin['name']) ? $admin['name'] : $admin['email']);
                    echo '<script>document.getElementById("loadingOverlay").style.display = "flex";</script>';
                    header("refresh:1;url=admin-dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "Admin account not found";
            }
        } elseif($user_type == 'owner') {
            $query = "SELECT * FROM owners WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result && $result->num_rows > 0) {
                $owner = $result->fetch_assoc();
                $stored_password = isset($owner['password']) ? $owner['password'] : (isset($owner['pwd']) ? $owner['pwd'] : '');
                
                if(password_verify($password, $stored_password)) {
                    $_SESSION['owner_id'] = $owner['owner_id'];
                    $_SESSION['owner_name'] = $owner['name'];
                    echo '<script>document.getElementById("loadingOverlay").style.display = "flex";</script>';
                    if($redirect) {
                        header("refresh:1;url=$redirect");
                    } else {
                        header("refresh:1;url=owner-dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "Owner account not found";
            }
        } elseif($user_type == 'buyer') {
            $query = "SELECT * FROM buyers WHERE email = '$email'";
            $result = $conn->query($query);
            
            if($result && $result->num_rows > 0) {
                $buyer = $result->fetch_assoc();
                $stored_password = isset($buyer['password']) ? $buyer['password'] : (isset($buyer['pwd']) ? $buyer['pwd'] : '');
                
                if(password_verify($password, $stored_password)) {
                    $_SESSION['buyer_id'] = $buyer['buyer_id'];
                    $_SESSION['buyer_name'] = $buyer['fname'] . ' ' . $buyer['lname'];
                    echo '<script>document.getElementById("loadingOverlay").style.display = "flex";</script>';
                    if($redirect) {
                        header("refresh:1;url=$redirect");
                    } else {
                        header("refresh:1;url=buyer-dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Invalid password";
                }
            } else {
                $error = "Buyer account not found";
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
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div style="text-align: center;">
            <div class="loader"></div>
            <div class="loading-text">🏠 Logging you in...</div>
        </div>
    </div>

    <!-- Floating Background Icons -->
    <div class="floating-bg">
        <div class="floating-icon">🏠</div>
        <div class="floating-icon">🏢</div>
        <div class="floating-icon">🏘️</div>
        <div class="floating-icon">🏗️</div>
        <div class="floating-icon">🏡</div>
        <div class="floating-icon">🌆</div>
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
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2 style="color: #E74C3C;">Welcome Back! 🏠</h2>
                <p style="color: #7F8C8D;">Login to your account</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="user_type">I am a:</label>
                    <select id="user_type" name="user_type" required>
                        <option value="buyer">🛒 Buyer</option>
                        <option value="owner">🏠 Property Owner</option>
                        <option value="admin">👨‍💼 Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required placeholder="your@email.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <span>🔑 Login</span>
                </button>

                <p class="auth-footer">
                    Don't have an account? <a href="register.php" style="color: #E74C3C;">Register here</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Animated Key Character -->
    <div class="cartoon-character" style="z-index: 100;">
        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
            <!-- Key -->
            <g class="keys-swing">
                <ellipse cx="100" cy="60" rx="20" ry="20" fill="#F39C12" stroke="#E67E22" stroke-width="2"/>
                <circle cx="100" cy="60" r="8" fill="white"/>
                <rect x="95" y="80" width="10" height="40" fill="#E67E22"/>
                <rect x="90" y="115" width="6" height="8" fill="#E67E22"/>
                <rect x="104" y="115" width="6" height="8" fill="#E67E22"/>
            </g>
        </svg>
    </div>

    <script>
        // Form submission with loading
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span>🔄 Logging in...</span>';
            submitBtn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>
