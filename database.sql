-- Real Estate Management System Database

USE real_estate_db;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Owners Table
CREATE TABLE IF NOT EXISTS owners (
    owner_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobile_no VARCHAR(20) NOT NULL,
    address TEXT,
    no_of_houses INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buyers Table
CREATE TABLE IF NOT EXISTS buyers (
    buyer_id INT PRIMARY KEY AUTO_INCREMENT,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobile_no VARCHAR(20) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Properties Table
CREATE TABLE IF NOT EXISTS properties (
    property_id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    property_name VARCHAR(200) NOT NULL,
    property_type ENUM('House', 'Apartment', 'Villa', 'Land', 'Commercial') NOT NULL,
    description TEXT,
    price DECIMAL(15, 2) NOT NULL,
    location VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    zip_code VARCHAR(20),
    bedrooms INT,
    bathrooms INT,
    area_sqft INT,
    year_built INT,
    amenities TEXT,
    image_path VARCHAR(255),
    status ENUM('Available', 'Sold', 'Pending') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES owners(owner_id) ON DELETE CASCADE
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    buyer_id INT NOT NULL,
    viewing_date DATE NOT NULL,
    viewing_time TIME NOT NULL,
    message TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES buyers(buyer_id) ON DELETE CASCADE
);

-- Insert default admin account
-- Password: admin123
INSERT INTO admins (name, email, password) VALUES 
('Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample owner accounts for testing
-- Password for all: password123
INSERT INTO owners (name, email, password, mobile_no, address, no_of_houses) VALUES 
('John Smith', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254712345678', '123 Main St, Nairobi', 2),
('Sarah Johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254723456789', '456 Oak Ave, Mombasa', 1);

-- Insert sample buyer accounts for testing
-- Password for all: password123
INSERT INTO buyers (fname, lname, email, password, mobile_no, address) VALUES 
('Michael', 'Brown', 'michael@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254734567890', '789 Pine Rd, Kisumu'),
('Emily', 'Davis', 'emily@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254745678901', '321 Elm St, Nakuru');

-- Insert sample properties
INSERT INTO properties (owner_id, property_name, property_type, description, price, location, city, state, zip_code, bedrooms, bathrooms, area_sqft, year_built, amenities, status) VALUES 
(1, 'Modern Family Home', 'House', 'Beautiful 4-bedroom house with a spacious garden and modern amenities.', 15000000.00, 'Kilimani', 'Nairobi', 'Nairobi County', '00100', 4, 3, 2500, 2018, 'Garden, Parking, Security, Water', 'Available'),
(1, 'Luxury Penthouse', 'Apartment', 'Stunning penthouse with panoramic city views and premium finishes.', 25000000.00, 'Westlands', 'Nairobi', 'Nairobi County', '00100', 3, 2, 1800, 2020, 'Gym, Pool, Parking, Security', 'Available'),
(2, 'Beachfront Villa', 'Villa', 'Exclusive beachfront property with private access to the ocean.', 45000000.00, 'Nyali', 'Mombasa', 'Mombasa County', '80100', 5, 4, 4000, 2019, 'Beach Access, Pool, Garden, Security', 'Available');

