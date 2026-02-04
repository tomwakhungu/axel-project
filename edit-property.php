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

// Fetch property details
$query = "SELECT * FROM properties WHERE property_id = $property_id AND owner_id = $owner_id";
$result = $conn->query($query);

if($result->num_rows == 0) {
    header("Location: owner-dashboard.php");
    exit();
}

$property = $result->fetch_assoc();
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_name = $conn->real_escape_string(trim($_POST['property_name']));
    $property_type = $conn->real_escape_string($_POST['property_type']);
    $description = $conn->real_escape_string(trim($_POST['description']));
    $price = (float)$_POST['price'];
    $location = $conn->real_escape_string(trim($_POST['location']));
    $city = $conn->real_escape_string(trim($_POST['city']));
    $state = isset($_POST['state']) ? $conn->real_escape_string(trim($_POST['state'])) : '';
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $square_feet = (int)$_POST['square_feet'];
    $amenities = isset($_POST['amenities']) ? $conn->real_escape_string(trim($_POST['amenities'])) : '';
    $status = $conn->real_escape_string($_POST['status']);
    $cover_image_choice = isset($_POST['cover_image']) ? $_POST['cover_image'] : 'image1';

    if(empty($property_name) || empty($description) || $price <= 0) {
        $error = "Please fill all required fields correctly";
    } else {
        $upload_dir = 'uploads/properties/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Keep existing images
        $image1 = $property['image1'];
        $image2 = $property['image2'];
        $image3 = $property['image3'];

        // Upload new image 1 if provided
        if(isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
            if($image1 && file_exists($upload_dir . $image1)) {
                unlink($upload_dir . $image1);
            }
            $image1 = time() . '_1_' . basename($_FILES['image1']['name']);
            move_uploaded_file($_FILES['image1']['tmp_name'], $upload_dir . $image1);
        }

        // Upload new image 2 if provided
        if(isset($_FILES['image2']) && $_FILES['image2']['error'] == 0) {
            if($image2 && file_exists($upload_dir . $image2)) {
                unlink($upload_dir . $image2);
            }
            $image2 = time() . '_2_' . basename($_FILES['image2']['name']);
            move_uploaded_file($_FILES['image2']['tmp_name'], $upload_dir . $image2);
        }

        // Upload new image 3 if provided
        if(isset($_FILES['image3']) && $_FILES['image3']['error'] == 0) {
            if($image3 && file_exists($upload_dir . $image3)) {
                unlink($upload_dir . $image3);
            }
            $image3 = time() . '_3_' . basename($_FILES['image3']['name']);
            move_uploaded_file($_FILES['image3']['tmp_name'], $upload_dir . $image3);
        }

        // Determine cover image
        $cover_image = $image1; // Default to image1
        if($cover_image_choice == 'image2' && $image2) {
            $cover_image = $image2;
        } elseif($cover_image_choice == 'image3' && $image3) {
            $cover_image = $image3;
        }

        $update_query = "UPDATE properties SET
                        property_name = '$property_name',
                        property_type = '$property_type',
                        description = '$description',
                        price = $price,
                        location = '$location',
                        city = '$city',
                        state = '$state',
                        bedrooms = $bedrooms,
                        bathrooms = $bathrooms,
                        square_feet = $square_feet,
                        amenities = '$amenities',
                        status = '$status',
                        image1 = " . ($image1 ? "'$image1'" : "NULL") . ",
                        image2 = " . ($image2 ? "'$image2'" : "NULL") . ",
                        image3 = " . ($image3 ? "'$image3'" : "NULL") . ",
                        cover_image = " . ($cover_image ? "'$cover_image'" : "NULL") . "
                        WHERE property_id = $property_id AND owner_id = $owner_id";

        if($conn->query($update_query)) {
            $success = "Property updated successfully! Redirecting...";
            echo '<script>
                setTimeout(function() {
                    window.location.href = "owner-dashboard.php";
                }, 2000);
            </script>';
        } else {
            $error = "Failed to update property: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .image-preview {
            width: 100%;
            height: 200px;
            border: 2px dashed #E0E0E0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.5rem;
            overflow: hidden;
            position: relative;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cover-image-selector {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #3498DB;
        }
        .cover-image-selector label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2C3E50;
        }
        .cover-radio-group {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .cover-radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cover-radio-item input[type="radio"] {
            width: auto;
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
                <li><a href="owner-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 1000px; margin: 0 auto;">
            <h2 class="text-center" style="margin-bottom: 0.5rem; color: #2C3E50;">Edit Property 🏠</h2>
            <p class="text-center" style="color: #7F8C8D; margin-bottom: 2rem;">Update your property details</p>

            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" id="editPropertyForm" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="color: #2C3E50; margin-bottom: 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">📝 Basic Information</h3>

                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="property_name">Property Name: *</label>
                        <input type="text" id="property_name" name="property_name" required value="<?php echo htmlspecialchars($property['property_name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="property_type">Property Type: *</label>
                        <select name="property_type" id="property_type" required>
                            <option value="House" <?php echo $property['property_type'] == 'House' ? 'selected' : ''; ?>>House</option>
                            <option value="Apartment" <?php echo $property['property_type'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                            <option value="Villa" <?php echo $property['property_type'] == 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            <option value="Condo" <?php echo $property['property_type'] == 'Condo' ? 'selected' : ''; ?>>Condo</option>
                            <option value="Land" <?php echo $property['property_type'] == 'Land' ? 'selected' : ''; ?>>Land</option>
                            <option value="Commercial" <?php echo $property['property_type'] == 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (KES): *</label>
                        <input type="number" id="price" name="price" required min="0" step="0.01" value="<?php echo $property['price']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="status">Status: *</label>
                        <select name="status" id="status" required>
                            <option value="Available" <?php echo $property['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                            <option value="Sold" <?php echo $property['status'] == 'Sold' ? 'selected' : ''; ?>>Sold</option>
                            <option value="Pending" <?php echo $property['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description: *</label>
                    <textarea name="description" id="description" required style="min-height: 120px;"><?php echo htmlspecialchars($property['description']); ?></textarea>
                </div>

                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">📍 Location Details</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="location">Street/Area: *</label>
                        <input type="text" id="location" name="location" required value="<?php echo htmlspecialchars($property['location']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="city">City: *</label>
                        <input type="text" id="city" name="city" required value="<?php echo htmlspecialchars($property['city']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="state">County/State:</label>
                        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($property['state'] ?? ''); ?>">
                    </div>
                </div>

                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">🏡 Property Features</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms: *</label>
                        <input type="number" id="bedrooms" name="bedrooms" required min="0" value="<?php echo $property['bedrooms']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="bathrooms">Bathrooms: *</label>
                        <input type="number" id="bathrooms" name="bathrooms" required min="0" value="<?php echo $property['bathrooms']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="square_feet">Square Feet: *</label>
                        <input type="number" id="square_feet" name="square_feet" required min="0" value="<?php echo $property['square_feet']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="amenities">Amenities (comma-separated):</label>
                    <textarea name="amenities" id="amenities" style="min-height: 80px;" placeholder="e.g., Swimming Pool, Gym, Parking, Garden"><?php echo htmlspecialchars($property['amenities'] ?? ''); ?></textarea>
                </div>

                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">📷 Property Images</h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="image1">Image 1:</label>
                        <input type="file" id="image1" name="image1" accept="image/*" onchange="previewImage(this, 'preview1')">
                        <div class="image-preview" id="preview1">
                            <?php if($property['image1']): ?>
                                <img src="uploads/properties/<?php echo $property['image1']; ?>" alt="Current Image 1">
                            <?php else: ?>
                                <span style="color: #7F8C8D;">📷 No Image</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image2">Image 2:</label>
                        <input type="file" id="image2" name="image2" accept="image/*" onchange="previewImage(this, 'preview2')">
                        <div class="image-preview" id="preview2">
                            <?php if($property['image2']): ?>
                                <img src="uploads/properties/<?php echo $property['image2']; ?>" alt="Current Image 2">
                            <?php else: ?>
                                <span style="color: #7F8C8D;">📷 No Image</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image3">Image 3:</label>
                        <input type="file" id="image3" name="image3" accept="image/*" onchange="previewImage(this, 'preview3')">
                        <div class="image-preview" id="preview3">
                            <?php if($property['image3']): ?>
                                <img src="uploads/properties/<?php echo $property['image3']; ?>" alt="Current Image 3">
                            <?php else: ?>
                                <span style="color: #7F8C8D;">📷 No Image</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cover Image Selection -->
                <div class="cover-image-selector">
                    <label>⭐ Choose Cover Photo (This will appear in property listings):</label>
                    <div class="cover-radio-group">
                        <div class="cover-radio-item">
                            <input type="radio" id="cover_image1" name="cover_image" value="image1" <?php echo (!isset($property['cover_image']) || $property['cover_image'] == $property['image1']) ? 'checked' : ''; ?>>
                            <label for="cover_image1" style="margin: 0;">Use Image 1</label>
                        </div>
                        <div class="cover-radio-item">
                            <input type="radio" id="cover_image2" name="cover_image" value="image2" <?php echo ($property['cover_image'] == $property['image2']) ? 'checked' : ''; ?>>
                            <label for="cover_image2" style="margin: 0;">Use Image 2</label>
                        </div>
                        <div class="cover-radio-item">
                            <input type="radio" id="cover_image3" name="cover_image" value="image3" <?php echo ($property['cover_image'] == $property['image3']) ? 'checked' : ''; ?>>
                            <label for="cover_image3" style="margin: 0;">Use Image 3</label>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="padding: 1rem 3rem; font-size: 1.1rem;">
                        ✓ Update Property
                    </button>
                    <a href="owner-dashboard.php" class="btn btn-secondary" style="padding: 1rem 3rem; font-size: 1.1rem;">
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

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Prevent double submission
        document.getElementById('editPropertyForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '🔄 Updating...';
            submitBtn.style.opacity = '0.6';
        });
    </script>
</body>
</html>
