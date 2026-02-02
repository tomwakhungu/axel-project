<?php
require_once 'includes/config.php';

if(!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['buyer_id'];
$buyer_query = "SELECT * FROM buyers WHERE buyer_id = $buyer_id";
$buyer = $conn->query($buyer_query)->fetch_assoc();

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $conn->real_escape_string(trim($_POST['fname']));
    $lname = $conn->real_escape_string(trim($_POST['lname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $mobile_no = $conn->real_escape_string(trim($_POST['mobile_no']));
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if(empty($fname) || empty($lname) || empty($email) || empty($mobile_no)) {
        $error = "Please fill all required fields";
    } elseif(!empty($new_password) && $new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email exists for another buyer
        $email_check = $conn->query("SELECT buyer_id FROM buyers WHERE email = '$email' AND buyer_id != $buyer_id");
        if($email_check->num_rows > 0) {
            $error = "Email already in use";
        } else {
            $update_query = "UPDATE buyers SET fname = '$fname', lname = '$lname', email = '$email', mobile_no = '$mobile_no'";
            
            if(!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query .= ", password = '$password_hash'";
            }
            
            $update_query .= " WHERE buyer_id = $buyer_id";
            
            if($conn->query($update_query)) {
                $success = "Profile updated successfully!";
                header("refresh:2;url=buyer-dashboard.php");
            } else {
                $error = "Failed to update profile";
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
    <title>Edit Profile - <?php echo SITE_NAME; ?></title>
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
                <li><a href="buyer-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <h2 class="text-center" style="margin-bottom: 0.5rem; color: #2C3E50;">Edit Profile</h2>
            <p class="text-center" style="color: #7F8C8D; margin-bottom: 2rem;">Update your account information</p>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="color: #2C3E50; margin-bottom: 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Personal Information</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="fname">First Name: *</label>
                        <input type="text" id="fname" name="fname" required value="<?php echo htmlspecialchars($buyer['fname']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="lname">Last Name: *</label>
                        <input type="text" id="lname" name="lname" required value="<?php echo htmlspecialchars($buyer['lname']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address: *</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($buyer['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mobile_no">Mobile Number: *</label>
                    <input type="text" id="mobile_no" name="mobile_no" required value="<?php echo htmlspecialchars($buyer['mobile_no']); ?>">
                </div>
                
                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Change Password (Optional)</h3>
                
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current password">
                    <small style="color: #7F8C8D;">Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">
                        ✓ Update Profile
                    </button>
                    <a href="buyer-dashboard.php" class="btn btn-secondary" style="padding: 1rem 3rem;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer" style="margin-top: 4rem;">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
