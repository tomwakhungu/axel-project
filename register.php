<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if($user_type == 'buyer') {
            $fname = $conn->real_escape_string(trim($_POST['fname']));
            $lname = $conn->real_escape_string(trim($_POST['lname']));
            $mobile = $conn->real_escape_string(trim($_POST['mobile']));
            $address = $conn->real_escape_string(trim($_POST['address']));
            
            // Check if email exists
            $check = $conn->query("SELECT * FROM buyers WHERE email = '$email'");
            if($check->num_rows > 0) {
                $error = "Email already registered";
            } else {
                // Check if table uses 'password' or 'pwd' column
                $columns = $conn->query("DESCRIBE buyers");
                $has_password = false;
                $has_pwd = false;
                while($col = $columns->fetch_assoc()) {
                    if($col['Field'] == 'password') $has_password = true;
                    if($col['Field'] == 'pwd') $has_pwd = true;
                }
                
                // Check if table uses 'mobile_no' or 'phone_no'
                $mobile_column = 'mobile_no';
                $columns = $conn->query("DESCRIBE buyers");
                while($col = $columns->fetch_assoc()) {
                    if($col['Field'] == 'phone_no') $mobile_column = 'phone_no';
                }
                
                if($has_password) {
                    $query = "INSERT INTO buyers (fname, lname, email, password, $mobile_column, address) 
                             VALUES ('$fname', '$lname', '$email', '$hashed_password', '$mobile', '$address')";
                } else {
                    $query = "INSERT INTO buyers (fname, lname, email, pwd, $mobile_column, address) 
                             VALUES ('$fname', '$lname', '$email', '$hashed_password', '$mobile', '$address')";
                }
                
                if($conn->query($query)) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Registration failed: " . $conn->error;
                }
            }
        } elseif($user_type == 'owner') {
            $name = $conn->real_escape_string(trim($_POST['name']));
            $mobile = $conn->real_escape_string(trim($_POST['mobile']));
            $address = $conn->real_escape_string(trim($_POST['address']));
            
            // Check if email exists
            $check = $conn->query("SELECT * FROM owners WHERE email = '$email'");
            if($check->num_rows > 0) {
                $error = "Email already registered";
            } else {
                // Check if table uses 'password' or 'pwd' column
                $columns = $conn->query("DESCRIBE owners");
                $has_password = false;
                $has_pwd = false;
                while($col = $columns->fetch_assoc()) {
                    if($col['Field'] == 'password') $has_password = true;
                    if($col['Field'] == 'pwd') $has_pwd = true;
                }
                
                if($has_password) {
                    $query = "INSERT INTO owners (name, email, password, mobile_no, address) 
                             VALUES ('$name', '$email', '$hashed_password', '$mobile', '$address')";
                } else {
                    $query = "INSERT INTO owners (name, email, pwd, mobile_no, address) 
                             VALUES ('$name', '$email', '$hashed_password', '$mobile', '$address')";
                }
                
                if($conn->query($query)) {
                    $success = "Registration successful! You can now login.";
                } else {
                    $error = "Registration failed: " . $conn->error;
                }
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
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-fields { display: none; }
        .form-fields.active { display: block; }
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
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="active">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Join our real estate platform</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <a href="login.php">Click here to login</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label for="user_type">I want to register as: *</label>
                    <select id="user_type" name="user_type" required onchange="toggleFields()">
                        <option value="">-- Select --</option>
                        <option value="buyer">Buyer</option>
                        <option value="owner">Property Owner</option>
                    </select>
                </div>

                <!-- Buyer Fields -->
                <div id="buyer-fields" class="form-fields">
                    <div class="form-group">
                        <label for="fname">First Name: *</label>
                        <input type="text" id="fname" name="fname" placeholder="Enter first name">
                    </div>

                    <div class="form-group">
                        <label for="lname">Last Name: *</label>
                        <input type="text" id="lname" name="lname" placeholder="Enter last name">
                    </div>
                </div>

                <!-- Owner Fields -->
                <div id="owner-fields" class="form-fields">
                    <div class="form-group">
                        <label for="name">Full Name: *</label>
                        <input type="text" id="name" name="name" placeholder="Enter full name">
                    </div>
                </div>

                <!-- Common Fields -->
                <div id="common-fields" class="form-fields">
                    <div class="form-group">
                        <label for="email">Email Address: *</label>
                        <input type="email" id="email" name="email" required placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label for="mobile">Mobile Number: *</label>
                        <input type="text" id="mobile" name="mobile" required placeholder="+254 700 000 000">
                    </div>

                    <div class="form-group">
                        <label for="address">Address: *</label>
                        <textarea id="address" name="address" rows="3" required placeholder="Your address"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="password">Password: *</label>
                        <input type="password" id="password" name="password" required placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password: *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>

                <p class="auth-footer">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        function toggleFields() {
            const userType = document.getElementById('user_type').value;
            const buyerFields = document.getElementById('buyer-fields');
            const ownerFields = document.getElementById('owner-fields');
            const commonFields = document.getElementById('common-fields');
            
            // Hide all
            buyerFields.classList.remove('active');
            ownerFields.classList.remove('active');
            commonFields.classList.remove('active');
            
            // Reset required attributes
            document.getElementById('fname').required = false;
            document.getElementById('lname').required = false;
            document.getElementById('name').required = false;
            
            // Show relevant fields
            if(userType === 'buyer') {
                buyerFields.classList.add('active');
                commonFields.classList.add('active');
                document.getElementById('fname').required = true;
                document.getElementById('lname').required = true;
            } else if(userType === 'owner') {
                ownerFields.classList.add('active');
                commonFields.classList.add('active');
                document.getElementById('name').required = true;
            }
        }
    </script>
</body>
</html>
