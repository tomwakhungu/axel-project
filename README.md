# 🏠 Axel Real Estate Management System

A modern, full-featured real estate property management system built with PHP, MySQL, and Apache. This platform enables property owners to list their properties and allows potential buyers to browse, search, and contact owners directly.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)

## ✨ Features

### For Property Owners
- 🔐 **Secure Authentication** - Register and login with encrypted passwords
- ➕ **Property Management** - Add, edit, and delete property listings
- 📸 **Multiple Images** - Upload up to 3 images per property
- ⭐ **Cover Photo Selection** - Choose which image displays as the cover
- 📊 **Dashboard** - Manage all your properties from one place
- 📝 **Detailed Listings** - Add descriptions, amenities, and property features

### For Buyers
- 🔍 **Advanced Search** - Filter by price, location, bedrooms, and property type
- 🏘️ **Browse Properties** - View all available properties with images
- 🖼️ **Image Gallery** - View all property photos in a beautiful lightbox
- 📱 **Contact Owners** - Direct email and phone contact information
- 🗺️ **Location Details** - Full address and city information

### Technical Features
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile
- 🎨 **Modern UI** - Clean, intuitive interface with emoji icons
- 🔒 **Secure** - SQL injection protection and password hashing
- 🚀 **Fast** - Optimized queries and efficient code
- 📷 **Image Management** - Automatic image upload and storage

## 🚀 Quick Start

### Prerequisites
- Ubuntu Desktop (20.04 or later)
- Apache 2.4+
- MySQL 5.7+ or MariaDB 10.3+
- PHP 7.4+ with extensions:
  - php-mysql
  - php-mbstring
  - php-gd
  - php-xml
- Git

### Installation

#### 1. Install Required Software
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install Apache, MySQL, PHP, and Git
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql php-cli php-mbstring php-xml php-gd git -y

# Verify installations
apache2 -v
mysql --version
php -v
git --version
```

#### 2. Configure MySQL
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE real_estate_db;
CREATE USER 'real_estate_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON real_estate_db.* TO 'real_estate_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Clone the Repository
```bash
# Navigate to web directory
cd /var/www/html

# Clone the project
sudo git clone https://github.com/tomwakhungu/axel-project.git axel-real-estate

# Set permissions
sudo chown -R www-data:www-data axel-real-estate
sudo chmod -R 755 axel-real-estate
```

#### 4. Create Uploads Directory
```bash
# Create directory for property images
sudo mkdir -p /var/www/html/axel-real-estate/uploads/properties

# Set permissions
sudo chmod -R 777 /var/www/html/axel-real-estate/uploads
```

#### 5. Configure Database Connection
```bash
# Edit config file
sudo nano /var/www/html/axel-real-estate/includes/config.php
```

Update these values:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'real_estate_user');
define('DB_PASS', 'your_secure_password');
define('DB_NAME', 'real_estate_db');
```

#### 6. Import Database Schema
```bash
mysql -u real_estate_user -p real_estate_db << 'EOSQL'

CREATE TABLE IF NOT EXISTS owners (
    owner_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS properties (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    property_name VARCHAR(200) NOT NULL,
    property_type VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    square_feet INT DEFAULT 0,
    amenities TEXT,
    status VARCHAR(20) DEFAULT 'Available',
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    image3 VARCHAR(255),
    cover_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES owners(owner_id) ON DELETE CASCADE
);

EOSQL
```

#### 7. Configure Apache (Optional but Recommended)
```bash
# Create virtual host
sudo nano /etc/apache2/sites-available/axel-real-estate.conf
```

Add this configuration:
```apache
<VirtualHost *:80>
    ServerAdmin admin@localhost
    DocumentRoot /var/www/html/axel-real-estate
    ServerName localhost

    <Directory /var/www/html/axel-real-estate>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/axel-error.log
    CustomLog ${APACHE_LOG_DIR}/axel-access.log combined
</VirtualHost>
```

```bash
# Enable the site and restart Apache
sudo a2ensite axel-real-estate.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 8. Access the Application
Open your browser and navigate to:
```
http://localhost/axel-real-estate
```

## 📁 Project Structure

```
axel-real-estate/
├── index.php                   # Home page
├── login.php                   # User login
├── register.php                # User registration
├── logout.php                  # Logout functionality
├── properties.php              # Browse all properties
├── property-details.php        # View property details
├── owner-dashboard.php         # Owner's dashboard
├── my-properties.php           # Owner's property list
├── add-property.php            # Add new property
├── edit-property.php           # Edit existing property
├── includes/
│   └── config.php             # Database configuration
├── assets/
│   └── css/
│       └── style.css          # Stylesheet
├── uploads/
│   └── properties/            # Property images storage
└── README.md                  # This file
```

## 🎯 Usage Guide

### For Property Owners

1. **Register an Account**
   - Click "Register" on the homepage
   - Fill in your details (name, email, phone, password)
   - Submit to create your account

2. **Login**
   - Use your email and password to login
   - You'll be redirected to your dashboard

3. **Add a Property**
   - Click "Add Property" from your dashboard
   - Fill in property details:
     - Property name
     - Type (House, Apartment, Villa, etc.)
     - Description
     - Price (in KES)
     - Location details
     - Bedrooms, bathrooms, square feet
     - Amenities
   - Upload up to 3 images
   - Select a cover photo
   - Click "Add Property"

4. **Manage Properties**
   - View all your properties in "My Properties"
   - Edit property details
   - Change cover photo
   - Update images
   - Delete properties

### For Buyers

1. **Browse Properties**
   - Visit the "Properties" page
   - Use filters to narrow down results:
     - Search by name/location
     - Filter by property type
     - Set price range
     - Select minimum bedrooms
     - Filter by city

2. **View Property Details**
   - Click on any property card
   - View all images in the gallery
   - Click main image for full-screen view
   - Read full description and amenities
   - View location details

3. **Contact Owner**
   - Contact information displayed on property details
   - Click "Send Email" to contact via email
   - Click "Call Now" to call directly

## 🔧 Configuration

### Database Settings
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');          // Database host
define('DB_USER', 'real_estate_user');   // Database username
define('DB_PASS', 'your_password');      // Database password
define('DB_NAME', 'real_estate_db');     // Database name
define('SITE_NAME', 'Axel Real Estate'); // Site name
```

### Upload Settings
- Maximum 3 images per property
- Supported formats: JPG, JPEG, PNG, GIF, WEBP
- Images stored in: `uploads/properties/`
- Recommended image size: 1200x800px minimum

## 🐛 Troubleshooting

### Common Issues and Solutions

#### 1. Permission Denied Errors
```bash
sudo chmod -R 777 /var/www/html/axel-real-estate/uploads
sudo chown -R www-data:www-data /var/www/html/axel-real-estate
```

#### 2. Database Connection Failed
- Check credentials in `includes/config.php`
- Verify MySQL is running: `sudo systemctl status mysql`
- Test connection: `mysql -u real_estate_user -p`

#### 3. Images Not Displaying
- Check uploads directory exists: `ls -la uploads/properties/`
- Verify permissions: `ls -ld uploads/`
- Check Apache error logs: `sudo tail -f /var/log/apache2/error.log`

#### 4. Apache Won't Start
```bash
# Check for errors
sudo apache2ctl configtest

# View error logs
sudo tail -f /var/log/apache2/error.log

# Restart Apache
sudo systemctl restart apache2
```

#### 5. Page Not Found (404)
- Verify Apache is running: `sudo systemctl status apache2`
- Check virtual host configuration
- Ensure `.htaccess` allows URL rewriting

### Viewing Logs
```bash
# Apache error logs
sudo tail -f /var/log/apache2/axel-error.log

# Apache access logs
sudo tail -f /var/log/apache2/axel-access.log

# MySQL error logs
sudo tail -f /var/log/mysql/error.log
```

## 🔒 Security Features

- **Password Hashing** - All passwords are hashed using PHP's `password_hash()`
- **SQL Injection Protection** - All queries use proper escaping
- **Session Management** - Secure session handling for authentication
- **File Upload Validation** - Only image files are allowed
- **Access Control** - Owners can only edit/delete their own properties

## 🚀 Performance Tips

1. **Enable Apache mod_rewrite**
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. **Enable PHP OpCache**
   ```bash
   sudo nano /etc/php/7.4/apache2/php.ini
   # Set: opcache.enable=1
   sudo systemctl restart apache2
   ```

3. **Optimize Images**
   - Compress images before uploading
   - Use WebP format for better compression
   - Recommended size: 1200x800px

4. **Database Optimization**
   ```sql
   -- Add indexes for better performance
   CREATE INDEX idx_status ON properties(status);
   CREATE INDEX idx_city ON properties(city);
   CREATE INDEX idx_price ON properties(price);
   ```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/YourFeature`)
3. Make your changes
4. Commit your changes (`git commit -m 'Add some feature'`)
5. Push to the branch (`git push origin feature/YourFeature`)
6. Open a Pull Request

### Coding Standards
- Use PHP 7.4+ features
- Follow PSR-12 coding standards
- Comment your code
- Test before submitting

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Tom Wakhungu**
- GitHub: [@tomwakhungu](https://github.com/tomwakhungu)

## 🙏 Acknowledgments

- Built with PHP, MySQL, and Apache
- Inspired by modern real estate platforms
- Thanks to all contributors

## 📞 Support

If you encounter any issues or have questions:

1. Check the [Troubleshooting](#-troubleshooting) section
2. Review [Common Issues](#common-issues-and-solutions)
3. Open an issue on GitHub
4. Contact the developer

## 🔮 Future Enhancements

- [ ] User profile management
- [ ] Advanced search with map integration
- [ ] Property comparison feature
- [ ] Favorite/wishlist functionality
- [ ] Email notifications
- [ ] Property reviews and ratings
- [ ] Admin panel for site management
- [ ] Payment integration for featured listings
- [ ] Mobile app version
- [ ] Multi-language support

## 📊 Database Schema

### Owners Table
| Column | Type | Description |
|--------|------|-------------|
| owner_id | INT | Primary key, auto-increment |
| full_name | VARCHAR(100) | Owner's full name |
| email | VARCHAR(100) | Unique email address |
| phone | VARCHAR(20) | Contact phone number |
| password | VARCHAR(255) | Hashed password |
| created_at | TIMESTAMP | Account creation date |

### Properties Table
| Column | Type | Description |
|--------|------|-------------|
| property_id | INT | Primary key, auto-increment |
| owner_id | INT | Foreign key to owners table |
| property_name | VARCHAR(200) | Name of the property |
| property_type | VARCHAR(50) | Type (House, Apartment, etc.) |
| description | TEXT | Full property description |
| price | DECIMAL(15,2) | Property price in KES |
| location | VARCHAR(255) | Street/area address |
| city | VARCHAR(100) | City name |
| state | VARCHAR(100) | County/state |
| bedrooms | INT | Number of bedrooms |
| bathrooms | INT | Number of bathrooms |
| square_feet | INT | Property size in sqft |
| amenities | TEXT | Comma-separated amenities |
| status | VARCHAR(20) | Available/Sold/Pending |
| image1 | VARCHAR(255) | First image filename |
| image2 | VARCHAR(255) | Second image filename |
| image3 | VARCHAR(255) | Third image filename |
| cover_image | VARCHAR(255) | Selected cover image |
| created_at | TIMESTAMP | Listing creation date |

## 📱 Screenshots

### Home Page
Modern landing page with featured properties and search functionality.

### Property Listings
Grid view of all available properties with filters and search.

### Property Details
Detailed view with image gallery, features, and contact information.

### Owner Dashboard
Manage all your properties from a centralized dashboard.

---

**⭐ If you find this project helpful, please give it a star!**

**🐛 Found a bug? Open an issue!**

**💡 Have a feature request? We'd love to hear it!**

---

Made with ❤️ by Tom Wakhungu
