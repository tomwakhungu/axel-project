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

$property_id = (int)$_GET['id'];
$owner_id = $_SESSION['owner_id'];

// Verify ownership
$query = "SELECT * FROM properties WHERE property_id = $property_id AND owner_id = $owner_id";
$result = $conn->query($query);

if($result->num_rows > 0) {
    $property = $result->fetch_assoc();
    
    // Delete images
    $upload_dir = 'uploads/properties/';
    if($property['image1'] && file_exists($upload_dir . $property['image1'])) {
        unlink($upload_dir . $property['image1']);
    }
    if($property['image2'] && file_exists($upload_dir . $property['image2'])) {
        unlink($upload_dir . $property['image2']);
    }
    if($property['image3'] && file_exists($upload_dir . $property['image3'])) {
        unlink($upload_dir . $property['image3']);
    }

    // Delete property
    $conn->query("DELETE FROM properties WHERE property_id = $property_id");
    $_SESSION['success'] = "Property deleted successfully!";
}

header("Location: owner-dashboard.php");
exit();
?>
