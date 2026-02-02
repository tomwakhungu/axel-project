<?php
require_once 'includes/config.php';

$error = '';
$success = '';
$user_type = isset($_GET['type']) ? $_GET['type'] : 'buyer';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = $_POST['user_type'];
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate
    if(empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if($user_type == 'buyer') {
            $fname = $conn->real_escape_string(trim($_POST['fname']));
            $lname = $conn->real_escape_string(trim($_POST['lname']));
            $phone = $conn->real_escape_string(trim($_POST['phone']));
            
            if(empty($fname) || empty($lname) || empty($phone)) {
                $error = "All fields are required";
            } else {
                // Check if email already exists
                $check_query = "SELECT * FROM buyers WHERE email = '$email'";
                if($conn->query($check_query)->num_rows > 0) {
                    $error = "Email already registered";
                } else {
                    $insert_query = "INSERT INTO buyers (fname, lname, email, pwd, phone_no, created_at) 
                                    VALUES ('$fname', '$lname', '$email', '$hashed_password', '$phone', NOW())";
                    
                    if($conn->query($insert_query)) {
                        $success = "Account created successfully! <a href='login.php'>Login here</a>";
                        $_POST = array(); // Clear form
                    } else {
                        $error = "Error creating account: " . $conn->error;
                    }
                }
            }
        } elseif($user_type == 'owner') {
            $name = $conn->real_escape_string(trim($_POST['name']));
            $phone = $conn->real_escape_string(trim($_POST['phone']));
            $id_number = $conn->real_escape_string(trim($_POST['id_number']));
            
            if(empty($name) || empty($phone) || empty($id_number)) {
                $error = "All fields are required";
            } else {
                // Check if email already exists
                $check_query = "SELECT * FROM owners WHERE email = '$email'";
                if($conn->query($check_query)->num_rows > 0) {
                    $error = "Email already registered";
                } else {
                    $insert_query = "INSERT INTO owners (name, email, pwd, mobile_no, id_number, created_at) 
                                    VALUES ('$name', '$email', '$hashed_password', '$phone', '$id_number', NOW())";
                    
                    if($conn->query($insert_query)) {
                        $success = "Account created successfully! <a href='login.php'>Login here</a>";
                        $_POST = array(); // Clear form
                    } else {
                        $error = "Error creating account: " . $conn->error;
                    }
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
        .register-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .register-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .register-tabs button {
            flex: 1;
            padding: 1rem;
            border: 2px solid #E0E0E0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            color: #2C3E50;
        }
        .register-tabs button.active {
            background: #3498DB;
            color: white;
            border-color: #3498DB;
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        .password-requirements {
            background: #ECF0F1;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .password-requirements ul {
            margin: 0.5rem 0 0 1rem;
            color: #7F8C8D;
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
                <li><a href="login.php" class="btn-primary">Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="register-container">
        <h2 class="text-center" style="margin-bottom: 2rem; color: #2C3E50;">Create Your Account</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">✓ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="register-tabs">
            <button class="tab-btn <?php echo ($user_type === 'buyer') ? 'active' : ''; ?>" onclick="switchTab('buyer')">
                👤 Buyer
            </button>
            <button class="tab-btn <?php echo ($user_type === 'owner') ? 'active' : ''; ?>" onclick="switchTab('owner')">
                🏢 Property Owner
            </button>
        </div>

        <!-- Buyer Registration Form -->
        <form id="buyer-form" class="form-section <?php echo ($user_type === 'buyer') ? 'active' : ''; ?>" method="POST" action="">
            <input type="hidden" name="user_type" value="buyer">
            
            <div class="form-group">
                <label for="buyer_fname">First Name</label>
                <input type="text" id="buyer_fname" name="fname" required placeholder="Enter your first name" 
                       value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="buyer_lname">Last Name</label>
                <input type="text" id="buyer_lname" name="lname" required placeholder="Enter your last name"
                       value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="buyer_phone">Phone Number</label>
                <input type="tel" id="buyer_phone" name="phone" required placeholder="e.g., 0700123456"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="buyer_email">Email Address</label>
                <input type="email" id="buyer_email" name="email" required placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="password-requirements">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>Minimum 6 characters</li>
                    <li>Use a mix of letters and numbers</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="buyer_password">Password</label>
                <input type="password" id="buyer_password" name="password" required placeholder="Create a password">
            </div>
            
            <div class="form-group">
                <label for="buyer_confirm_password">Confirm Password</label>
                <input type="password" id="buyer_confirm_password" name="confirm_password" required placeholder="Confirm your password">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                Create Buyer Account
            </button>
        </form>

        <!-- Owner Registration Form -->
        <form id="owner-form" class="form-section <?php echo ($user_type === 'owner') ? 'active' : ''; ?>" method="POST" action="">
            <input type="hidden" name="user_type" value="owner">
            
            <div class="form-group">
                <label for="owner_name">Full Name</label>
                <input type="text" id="owner_name" name="name" required placeholder="Enter your full name"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="owner_id">ID Number</label>
                <input type="text" id="owner_id" name="id_number" required placeholder="Enter your ID number"
                       value="<?php echo isset($_POST['id_number']) ? htmlspecialchars($_POST['id_number']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="owner_phone">Phone Number</label>
                <input type="tel" id="owner_phone" name="phone" required placeholder="e.g., 0700123456"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="owner_email">Email Address</label>
                <input type="email" id="owner_email" name="email" required placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="password-requirements">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>Minimum 6 characters</li>
                    <li>Use a mix of letters and numbers</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="owner_password">Password</label>
                <input type="password" id="owner_password" name="password" required placeholder="Create a password">
            </div>
            
            <div class="form-group">
                <label for="owner_confirm_password">Confirm Password</label>
                <input type="password" id="owner_confirm_password" name="confirm_password" required placeholder="Confirm your password">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                Create Owner Account
            </button>
        </form>

        <p class="text-center mt-2">
            Already have an account? <a href="login.php" style="color: #E74C3C; font-weight: bold;">Login here</a>
        </p>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        function switchTab(type) {
            // Hide all forms
            document.getElementById('buyer-form').classList.remove('active');
            document.getElementById('owner-form').classList.remove('active');
            
            // Remove active class from buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            // Show selected form
            if(type === 'buyer') {
                document.getElementById('buyer-form').classList.add('active');
            } else {
                document.getElementById('owner-form').classList.add('active');
            }
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
