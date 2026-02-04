<?php
require_once 'includes/config.php';

echo "<h2>Creating test accounts...</h2>";

// Create admin
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("DELETE FROM admins WHERE email = 'admin@example.com'");
$result = $conn->query("INSERT INTO admins (name, email, password) VALUES ('Admin', 'admin@example.com', '$admin_pass')");
echo "Admin created: " . ($result ? "✓ Success" : "✗ Failed - " . $conn->error) . "<br>";

// Create owners
$owner_pass = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("DELETE FROM owners WHERE email IN ('john@example.com', 'sarah@example.com')");

$result = $conn->query("INSERT INTO owners (name, email, password, mobile_no, address, no_of_houses) 
VALUES ('John Smith', 'john@example.com', '$owner_pass', '+254712345678', '123 Main St, Nairobi', 0)");
echo "Owner John created: " . ($result ? "✓ Success" : "✗ Failed - " . $conn->error) . "<br>";

$result = $conn->query("INSERT INTO owners (name, email, password, mobile_no, address, no_of_houses) 
VALUES ('Sarah Johnson', 'sarah@example.com', '$owner_pass', '+254723456789', '456 Oak Ave, Mombasa', 0)");
echo "Owner Sarah created: " . ($result ? "✓ Success" : "✗ Failed - " . $conn->error) . "<br>";

// Create buyers
$buyer_pass = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("DELETE FROM buyers WHERE email IN ('michael@example.com', 'emily@example.com')");

$result = $conn->query("INSERT INTO buyers (fname, lname, email, password, mobile_no, address) 
VALUES ('Michael', 'Brown', 'michael@example.com', '$buyer_pass', '+254734567890', '789 Pine Rd, Kisumu')");
echo "Buyer Michael created: " . ($result ? "✓ Success" : "✗ Failed - " . $conn->error) . "<br>";

$result = $conn->query("INSERT INTO buyers (fname, lname, email, password, mobile_no, address) 
VALUES ('Emily', 'Davis', 'emily@example.com', '$buyer_pass', '+254745678901', '321 Elm St, Nakuru')");
echo "Buyer Emily created: " . ($result ? "✓ Success" : "✗ Failed - " . $conn->error) . "<br>";

echo "<br><h3>Login Credentials:</h3>";
echo "<strong>Buyer:</strong> michael@example.com / password123<br>";
echo "<strong>Owner:</strong> john@example.com / password123<br>";
echo "<strong>Admin:</strong> admin@example.com / admin123<br><br>";
echo "<a href='login.php'>Go to Login Page</a>";
?>
