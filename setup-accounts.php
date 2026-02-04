<?php
// Database connection - USE YOUR MYSQL PASSWORD
$conn = new mysqli('localhost', 'root', 'newpassword123', 'real_estate_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Setting up accounts...</h2>";

// First, let's check what columns exist
echo "<h3>Checking database structure...</h3>";
$result = $conn->query("DESCRIBE buyers");
echo "Buyers table columns:<br>";
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . "<br>";
}

$result = $conn->query("DESCRIBE owners");
echo "<br>Owners table columns:<br>";
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . "<br>";
}

$result = $conn->query("DESCRIBE admins");
echo "<br>Admins table columns:<br>";
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . "<br>";
}

echo "<br><h3>Updating existing accounts with new passwords...</h3>";

// Update buyer passwords
$buyer_pass = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("UPDATE buyers SET pwd = '$buyer_pass' WHERE email = 'michael@realestateHub.com'");
echo "✓ Updated Michael's password<br>";

// Update owner passwords  
$owner_pass = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("UPDATE owners SET pwd = '$owner_pass' WHERE email = 'john@realestateHub.com'");
echo "✓ Updated John's password<br>";

// Check if admins table exists and has data
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$row = $result->fetch_assoc();

if($row['count'] == 0) {
    echo "<br>No admin found. Creating admin account...<br>";
    
    // Check if admins table has 'password' or 'pwd' column
    $columns = $conn->query("DESCRIBE admins");
    $has_password = false;
    $has_pwd = false;
    
    while($col = $columns->fetch_assoc()) {
        if($col['Field'] == 'password') $has_password = true;
        if($col['Field'] == 'pwd') $has_pwd = true;
    }
    
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    if($has_password) {
        $conn->query("INSERT INTO admins (name, email, password) VALUES ('Admin', 'admin@realestateHub.com', '$admin_pass')");
    } else {
        $conn->query("INSERT INTO admins (name, email, pwd) VALUES ('Admin', 'admin@realestateHub.com', '$admin_pass')");
    }
    echo "✓ Admin account created<br>";
} else {
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Try both column names
    $conn->query("UPDATE admins SET password = '$admin_pass' WHERE email LIKE '%admin%'");
    $conn->query("UPDATE admins SET pwd = '$admin_pass' WHERE email LIKE '%admin%'");
    echo "✓ Updated admin password<br>";
}

echo "<br><h3>Current Accounts:</h3>";

$result = $conn->query("SELECT buyer_id, fname, lname, email FROM buyers");
echo "<strong>Buyers:</strong><br>";
while($row = $result->fetch_assoc()) {
    echo "- {$row['fname']} {$row['lname']} ({$row['email']})<br>";
}

$result = $conn->query("SELECT owner_id, name, email FROM owners");
echo "<br><strong>Owners:</strong><br>";
while($row = $result->fetch_assoc()) {
    echo "- {$row['name']} ({$row['email']})<br>";
}

$result = $conn->query("SELECT * FROM admins");
echo "<br><strong>Admins:</strong><br>";
while($row = $result->fetch_assoc()) {
    echo "- {$row['name']} ({$row['email']})<br>";
}

echo "<br><br><h3>Login Credentials:</h3>";
echo "<strong>Buyer:</strong> michael@realestateHub.com / password123<br>";
echo "<strong>Owner:</strong> john@realestateHub.com / password123<br>";
echo "<strong>Admin:</strong> admin@realestateHub.com / admin123<br><br>";
echo "<a href='login.php'>Go to Login Page</a>";

$conn->close();
?>
