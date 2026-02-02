<?php
require_once 'includes/config.php';

if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: owner-dashboard.php");
    exit();
}

$booking_id = (int)$_GET['id'];
$owner_id = $_SESSION['owner_id'];

// Verify ownership and update status
$query = "UPDATE bookings b
          JOIN properties p ON b.property_id = p.property_id
          SET b.status = 'Confirmed'
          WHERE b.booking_id = $booking_id AND p.owner_id = $owner_id";

if($conn->query($query)) {
    $_SESSION['success'] = "Booking confirmed successfully!";
} else {
    $_SESSION['error'] = "Failed to confirm booking.";
}

header("Location: owner-dashboard.php");
exit();
?>
