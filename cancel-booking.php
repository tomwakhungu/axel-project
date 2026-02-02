<?php
require_once 'includes/config.php';

if(!isset($_SESSION['buyer_id']) && !isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])) {
    $redirect = isset($_SESSION['buyer_id']) ? 'buyer-dashboard.php' : 'owner-dashboard.php';
    header("Location: $redirect");
    exit();
}

$booking_id = (int)$_GET['id'];

if(isset($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $query = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = $booking_id AND buyer_id = $buyer_id";
    $redirect = 'buyer-dashboard.php';
} else {
    $owner_id = $_SESSION['owner_id'];
    $query = "UPDATE bookings b
              JOIN properties p ON b.property_id = p.property_id
              SET b.status = 'Cancelled'
              WHERE b.booking_id = $booking_id AND p.owner_id = $owner_id";
    $redirect = 'owner-dashboard.php';
}

if($conn->query($query)) {
    $_SESSION['success'] = "Booking cancelled successfully!";
} else {
    $_SESSION['error'] = "Failed to cancel booking.";
}

header("Location: $redirect");
exit();
?>
