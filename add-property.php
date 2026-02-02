<?php
require_once 'includes/config.php';

if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];
$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_name = $conn->real_escape_string(trim($_POST['property_name']));
    $property_type = $conn->real_escape_string($_POST['property_type']);
    $description = $conn->real_escape_string(trim($_POST['description']));
    $price = (float)$_POST['price'];
    $location = $conn->real_escape_string(trim($_POST['location']));
    $city = $conn->real_escape_string(trim($_POST['city']));
    $state = $conn->real_escape_string(trim($_POST['state']));
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $square_feet = (int)$_POST['square_feet'];
    $amenities = $conn->real_escape_string(trim($_POST['amenities']));
    $status = 'Available';
    
    if(empty($property_name) || empty($description) || $price <= 0) {
        $error = "Please fill all required fields correctly";
    } else {
        // Handle image uploads
        $upload_dir = 'uploads/properties/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $image1 = '';
        $image2 = '';
        $image3 = '';
        
        // Upload image 1
        if(isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
            $image1 = time() . '_1_' . basename($_FILES['image1']['name']);
            move_uploaded_file($_FILES['image1']['tmp_name'], $upload_dir . $image1);
        }
        
        // Upload image 2
        if(isset($_FILES['image2']) && $_FILES['image2']['error'] == 0) {
            $image2 = time() . '_2_' . basename($_FILES['image2']['name']);
            move_uploaded_file($_FILES['image2']['tmp_name'], $upload_dir . $image2);
        }
        
        // Upload image 3
        if(isset($_FILES['image3']) && $_FILES['image3']['error'] == 0) {
            $image3 = time() . '_3_' . basename($_FILES['image3']['name']);
            move_uploaded_file($_FILES['image3']['tmp_name'], $upload_dir . $image3);
        }
        
        $insert_query = "INSERT INTO properties (owner_id, property_name, property_type, description, price, location, city, state, bedrooms, bathrooms, square_feet, amenities, status, image1, image2, image3) 
                        VALUES ($owner_id, '$property_name', '$property_type', '$description', $price, '$location', '$city', '$state', $bedrooms, $bathrooms, $square_feet, '$amenities', '$status', '$image1', '$image2', '$image3')";
        
        if($conn->query($insert_query)) {
            $success = "Property added successfully!";
            header("refresh:2;url=owner-dashboard.php");
        } else {
            $error = "Failed to add property. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - <?php echo SITE_NAME; ?></title>
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
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            <h2 class="text-center" style="margin-bottom: 0.5rem; color: #2C3E50;">Add New Property</h2>
            <p class="text-center" style="color: #7F8C8D; margin-bottom: 2rem;">Fill in the details to list your property</p>
            
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3 style="color: #2C3E50; margin-bottom: 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Basic Information</h3>
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="property_name">Property Name: *</label>
                        <input type="text" id="property_name" name="property_name" required placeholder="e.g., Modern Villa in Westlands">
                    </div>
                    
                    <div class="form-group">
                        <label for="property_type">Property Type: *</label>
                        <select name="property_type" id="property_type" required>
                            <option value="">Select Type</option>
                            <option value="House">House</option>
                            <option value="Apartment">Apartment</option>
                            <option value="Villa">Villa</option>
                            <option value="Condo">Condo</option>
                            <option value="Land">Land</option>
                            <option value="Commercial">Commercial</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (KES): *</label>
                        <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="5000000">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description: *</label>
                    <textarea name="description" id="description" required placeholder="Describe your property..." style="min-height: 120px;"></textarea>
                </div>
                
                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Location Details</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="location">Street/Area: *</label>
                        <input type="text" id="location" name="location" required placeholder="e.g., Limuru Road">
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City: *</label>
                        <input type="text" id="city" name="city" required placeholder="e.g., Nairobi">
                    </div>
                    
                    <div class="form-group">
                        <label for="state">County/State: *</label>
                        <input type="text" id="state" name="state" required placeholder="e.g., Nairobi County">
                    </div>
                </div>
                
                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Property Features</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms: *</label>
                        <input type="number" id="bedrooms" name="bedrooms" required min="0" placeholder="3">
                    </div>
                    
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms: *</label>
                        <input type="number" id="bathrooms" name="bathrooms" required min="0" placeholder="2">
                    </div>
                    
                    <div class="form-group">
                        <label for="square_feet">Square Feet: *</label>
                        <input type="number" id="square_feet" name="square_feet" required min="0" placeholder="1500">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="amenities">Amenities (comma-separated):</label>
                    <textarea name="amenities" id="amenities" placeholder="Parking, Swimming Pool, Garden, Security..." style="min-height: 80px;"></textarea>
                    <small style="color: #7F8C8D;">Separate each amenity with a comma</small>
                </div>
                
                <h3 style="color: #2C3E50; margin: 2rem 0 1.5rem; border-bottom: 2px solid #E74C3C; padding-bottom: 0.5rem;">Property Images</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="image1">Main Image: *</label>
                        <input type="file" id="image1" name="image1" accept="image/*" required onchange="previewImage(this, 'preview1')">
                        <div class="image-preview" id="preview1">
                            <span style="color: #7F8C8D;">📷 Image Preview</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image2">Image 2:</label>
                        <input type="file" id="image2" name="image2" accept="image/*" onchange="previewImage(this, 'preview2')">
                        <div class="image-preview" id="preview2">
                            <span style="color: #7F8C8D;">📷 Image Preview</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image3">Image 3:</label>
                        <input type="file" id="image3" name="image3" accept="image/*" onchange="previewImage(this, 'preview3')">
                        <div class="image-preview" id="preview3">
                            <span style="color: #7F8C8D;">📷 Image Preview</span>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                    <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                        ✓ Add Property
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

    <script src="assets/js/main.js"></script>
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
    </script>
</body>
</html>
