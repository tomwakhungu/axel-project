<?php
require_once 'includes/config.php';

echo "<h2>Resetting all passwords...</h2>";

// Reset admin password
$admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
$result = $conn->query("UPDATE admins SET password = '$admin_pass' WHERE email = 'admin@example.com'");
echo "Admin password reset: " . ($result ? "✓ Success" : "✗ Failed") . "<br>";

// Reset owner passwords
$owner_pass = password_hash('password123', PASSWORD_DEFAULT);
$result = $conn->query("UPDATE owners SET password = '$owner_pass' WHERE email = 'john@example.com'");
echo "Owner (john@example.com) password reset: " . ($result ? "✓ Success" : "✗ Failed") . "<br>";

$result = $conn->query("UPDATE owners SET password = '$owner_pass' WHERE email = 'sarah@example.com'");
echo "Owner (sarah@example.com) password reset: " . ($result ? "✓ Success" : "✗ Failed") . "<br>";

// Reset buyer passwords
$buyer_pass = password_hash('password123', PASSWORD_DEFAULT);
$result = $conn->query("UPDATE buyers SET password = '$buyer_pass' WHERE email = 'michael@example.com'");
echo "Buyer (michael@example.com) password reset: " . ($result ? "✓ Success" : "✗ Failed") . "<br>";

$result = $conn->query("UPDATE buyers SET password = '$buyer_pass' WHERE email = 'emily@example.com'");
echo "Buyer (emily@example.com) password reset: " . ($result ? "✓ Success" : "✗ Failed") . "<br>";

echo "<br><h3>Verification:</h3>";

// Verify buyers
$buyers = $conn->query("SELECT buyer_id, email, fname, lname FROM buyers");
echo "<strong>Buyers in database:</strong><br>";
while($buyer = $buyers->fetch_assoc()) {
    echo "- ID: {$buyer['buyer_id']}, Email: {$buyer['email']}, Name: {$buyer['fname']} {$buyer['lname']}<br>";
}

echo "<br>";

// Verify owners
$owners = $conn->query("SELECT owner_id, email, name FROM owners");
echo "<strong>Owners in database:</strong><br>";
while($owner = $owners->fetch_assoc()) {
    echo "- ID: {$owner['owner_id']}, Email: {$owner['email']}, Name: {$owner['name']}<br>";
}

echo "<br>";

// Verify admins
$admins = $conn->query("SELECT admin_id, email, name FROM admins");
echo "<strong>Admins in database:</strong><br>";
while($admin = $admins->fetch_assoc()) {
    echo "- ID: {$admin['admin_id']}, Email: {$admin['email']}, Name: {$admin['name']}<br>";
}

echo "<br><br>";
echo "<h3>Login Credentials:</h3>";
echo "<strong>Buyer:</strong> michael@example.com / password123<br>";
echo "<strong>Owner:</strong> john@example.com / password123<br>";
echo "<strong>Admin:</strong> admin@example.com / admin123<br><br>";
echo "<a href='login.php'>Go to Login Page</a>";
?>
