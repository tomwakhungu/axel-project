<?php
$conn = new mysqli('localhost', 'root', 'newpassword123', 'real_estate_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Fixing Admin Account...</h2>";

// Check table structure
$columns = $conn->query("DESCRIBE admins");
echo "<h3>Admin table columns:</h3>";
while($col = $columns->fetch_assoc()) {
    echo "- " . $col['Field'] . "<br>";
}

echo "<br><h3>Creating/Updating Admin...</h3>";

// Delete existing admin
$conn->query("DELETE FROM admins");
echo "Cleared existing admins<br>";

// Create new admin with correct password - using username and pwd columns
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$admin_email = 'admin@realestateHub.com';
$admin_username = 'admin';

// Insert using the correct column names
$result = $conn->query("INSERT INTO admins (username, email, pwd) VALUES ('$admin_username', '$admin_email', '$admin_pass')");

if($result) {
    echo "✓ Admin account created successfully!<br>";
} else {
    echo "✗ Failed to create admin: " . $conn->error . "<br>";
}

// Verify
$verify = $conn->query("SELECT * FROM admins");
echo "<br><h3>Admin accounts in database:</h3>";
if($verify && $verify->num_rows > 0) {
    while($admin = $verify->fetch_assoc()) {
        echo "- ID: {$admin['admin_id']}, Email: {$admin['email']}, Username: {$admin['username']}<br>";
        
        // Test password verification
        if(password_verify('admin123', $admin['pwd'])) {
            echo "  ✓ Password verification: SUCCESS<br>";
        } else {
            echo "  ✗ Password verification: FAILED<br>";
        }
    }
} else {
    echo "No admin accounts found!<br>";
}

echo "<br><h3>Login Credentials:</h3>";
echo "<strong>Email:</strong> admin@realestateHub.com<br>";
echo "<strong>Password:</strong> admin123<br><br>";
echo "<a href='login.php'>Go to Login Page</a>";

$conn->close();
?>
