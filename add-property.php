<?php
require_once 'includes/config.php';

// Check if owner is logged in
if(!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];
$success = '';
$error = '';

// Check what columns exist in properties table
$columns_query = "DESCRIBE properties";
$columns_result = $conn->query($columns_query);
$existing_columns = [];
while($col = $columns_result->fetch_assoc()) {
    $existing_columns[] = $col['Field'];
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $property_name = $conn->real_escape_string(trim($_POST['property_name']));
        $property_type = $conn->real_escape_string($_POST['property_type']);
        $description = $conn->real_escape_string(trim($_POST['description']));
        $price = floatval($_POST['price']);
        $location = $conn->real_escape_string(trim($_POST['location']));
        $city = $conn->real_escape_string(trim($_POST['city']));
        $state = isset($_POST['state']) ? $conn->real_escape_string(trim($_POST['state'])) : '';
        $zip_code = isset($_POST['zip_code']) ? $conn->real_escape_string(trim($_POST['zip_code'])) : '';
        $bedrooms = isset($_POST['bedrooms']) ? intval($_POST['bedrooms']) : 0;
        $bathrooms = isset($_POST['bathrooms']) ? intval($_POST['bathrooms']) : 0;
        $area_sqft = isset($_POST['area_sqft']) ? intval($_POST['area_sqft']) : 0;
        $year_built = isset($_POST['year_built']) ? intval($_POST['year_built']) : 0;
        $amenities = isset($_POST['amenities']) ? $conn->real_escape_string(trim($_POST['amenities'])) : '';
        $status = $conn->real_escape_string($_POST['status']);
        
        $image_path = '';
        
        // Handle image upload
        if(isset($_FILES['property_image']) && $_FILES['property_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['property_image']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(in_array(strtolower($filetype), $allowed)) {
                $new_filename = 'property_' . time() . '.' . $filetype;
                $upload_path = 'uploads/properties/' . $new_filename;
                
                // Make sure upload directory exists
                if(!is_dir('uploads/properties')) {
                    mkdir('uploads/properties', 0755, true);
                }
                
                if(move_uploaded_file($_FILES['property_image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                }
            }
        }
        
        // Build query based on existing columns
        $columns = ['owner_id', 'property_name', 'property_type', 'description', 'price', 'location', 'city', 'status'];
        $values = [$owner_id, "'$property_name'", "'$property_type'", "'$description'", $price, "'$location'", "'$city'", "'$status'"];
        
        // Add optional columns if they exist
        if(in_array('state', $existing_columns) && !empty($state)) {
            $columns[] = 'state';
            $values[] = "'$state'";
        }
        if(in_array('zip_code', $existing_columns) && !empty($zip_code)) {
            $columns[] = 'zip_code';
            $values[] = "'$zip_code'";
        }
        if(in_array('bedrooms', $existing_columns) && $bedrooms > 0) {
            $columns[] = 'bedrooms';
            $values[] = $bedrooms;
        }
        if(in_array('bathrooms', $existing_columns) && $bathrooms > 0) {
            $columns[] = 'bathrooms';
            $values[] = $bathrooms;
        }
        if(in_array('area_sqft', $existing_columns) && $area_sqft > 0) {
            $columns[] = 'area_sqft';
            $values[] = $area_sqft;
        }
        if(in_array('year_built', $existing_columns) && $year_built > 0) {
            $columns[] = 'year_built';
            $values[] = $year_built;
        }
        if(in_array('amenities', $existing_columns) && !empty($amenities)) {
            $columns[] = 'amenities';
            $values[] = "'$amenities'";
        }
        if(in_array('image_path', $existing_columns) && !empty($image_path)) {
            $columns[] = 'image_path';
            $values[] = "'$image_path'";
        }
        
        $query = "INSERT INTO properties (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        
        if($conn->query($query)) {
            $success = "Property added successfully!";
            // Redirect after 2 seconds
            echo "<script>setTimeout(function(){ window.location.href='owner-dashboard.php'; }, 2000);</script>";
        } else {
            $error = "Failed to add property: " . $conn->error;
        }
    } catch(Exception $e) {
        $error = "Error: " . $e->getMessage();
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
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="owner-dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 2rem 0;">
        <a href="owner-dashboard.php" style="display: inline-block; color: #E74C3C; text-decoration: none; margin-bottom: 2rem;">
            ← Back to Dashboard
        </a>

        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h1 style="color: #2C3E50; margin-bottom: 0.5rem;">Add New Property</h1>
                <p style="color: #7F8C8D; margin-bottom: 2rem;">Fill in the details below to list your property</p>

                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?> Redirecting to dashboard...</div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="property_name">Property Name: *</label>
                            <input type="text" id="property_name" name="property_name" required placeholder="e.g., Modern Family Home">
                        </div>

                        <div class="form-group">
                            <label for="property_type">Property Type: *</label>
                            <select id="property_type" name="property_type" required>
                                <option value="House">House</option>
                                <option value="Apartment">Apartment</option>
                                <option value="Villa">Villa</option>
                                <option value="Land">Land</option>
                                <option value="Commercial">Commercial</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (KES): *</label>
                            <input type="number" id="price" name="price" required placeholder="e.g., 5000000" step="0.01" min="0">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="description">Description: *</label>
                            <textarea id="description" name="description" rows="4" required placeholder="Describe your property..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="location">Location/Street: *</label>
                            <input type="text" id="location" name="location" required placeholder="e.g., Kilimani Road">
                        </div>

                        <div class="form-group">
                            <label for="city">City: *</label>
                            <input type="text" id="city" name="city" required placeholder="e.g., Nairobi">
                        </div>

                        <div class="form-group">
                            <label for="state">County:</label>
                            <input type="text" id="state" name="state" placeholder="e.g., Nairobi County">
                        </div>

                        <div class="form-group">
                            <label for="zip_code">Zip/Postal Code:</label>
                            <input type="text" id="zip_code" name="zip_code" placeholder="e.g., 00100">
                        </div>

                        <div class="form-group">
                            <label for="bedrooms">Bedrooms:</label>
                            <input type="number" id="bedrooms" name="bedrooms" placeholder="e.g., 3" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="bathrooms">Bathrooms:</label>
                            <input type="number" id="bathrooms" name="bathrooms" placeholder="e.g., 2" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="area_sqft">Area (sq ft):</label>
                            <input type="number" id="area_sqft" name="area_sqft" placeholder="e.g., 2500" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="year_built">Year Built:</label>
                            <input type="number" id="year_built" name="year_built" placeholder="e.g., 2020" min="1900" max="2030" value="0">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="amenities">Amenities (comma separated):</label>
                            <input type="text" id="amenities" name="amenities" placeholder="e.g., Parking, Garden, Security, Pool">
                        </div>

                        <div class="form-group">
                            <label for="status">Status: *</label>
                            <select id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Pending">Pending</option>
                                <option value="Sold">Sold</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="property_image">Property Image:</label>
                            <input type="file" id="property_image" name="property_image" accept="image/*">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem;">Add Property</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2025 <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
