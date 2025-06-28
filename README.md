# 🎨 HandCraft Shop - SPL Final Project

A full-featured e-commerce platform for handmade products, built with PHP/MySQL and modern frontend technologies.

## 🚀 Live Demo

**Website:** https://100-percent-correct-decision.infinityfreeapp.com

Test the complete functionality including shopping cart, favorites, multilingual interface, and order placement.

## 📋 Project Overview

This project demonstrates a complete e-commerce solution for selling handmade products with modern web development practices and security features.

### ✅ Core Requirements Met
- **Site Structure**: Home, Products, Cart with shared header/body/footer
- **Product Functionality**: Product listing with images, descriptions, pricing, quantity selection
- **Session Management**: Cart persistence using `$_SESSION`
- **PHP Classes**: Page, ProductPage, CartPage, Product with full method implementation
- **POST Forms**: All actions use POST methods for security
- **Additional Features**: Contact form, unified design, product filtering

### 🚀 Enhanced Features
- 🔍 **Live Search** with filtering capabilities
- ❤️ **Favorites System** with AJAX functionality  
- 🛒 **Complete Checkout** with multiple payment/delivery options
- 🌐 **Bilingual Support** (Ukrainian/English)
- 🛡️ **Security Layer** (CSRF, XSS protection, Rate Limiting)
- 📱 **Responsive Design** for all devices
- ⚡ **AJAX Operations** without page reloads

## 🏗️ Architecture

### Backend
- **PHP 8.0+** with Object-Oriented Programming
- **MySQL** with optimized database schema
- **PDO** for secure database operations
- **Singleton Pattern** for Database class
- **Security-first** approach with input validation

### Frontend
- **Modern JavaScript** (ES6+) with classes
- **CSS Grid/Flexbox** for responsive layouts
- **AJAX** for seamless user experience
- **Progressive Enhancement** methodology

## 📁 Complete Project Structure (Actual Files)

```
spl-final-project/
├── 📄 cart.php                     # Shopping cart management
├── 📄 checkout.php                 # Order placement and processing
├── 📄 contact.php                  # Contact form with validation
├── 📄 favorites.php                # User favorites management
├── 📄 index.php                    # Home page with featured products
├── 📄 products.php                 # Product catalog with filters
├── 📄 search.php                   # Advanced search functionality
├── 📄 README.md                    # Project documentation
├── 📄 .gitignore                   # Git ignore rules
├── 📁 ajax/                        # AJAX Request Handlers
│   ├── 📄 add_to_cart.php          # Add product to cart
│   ├── 📄 add_to_favorites.php     # Toggle favorites
│   ├── 📄 change_language.php      # Language switching
│   ├── 📄 get_cart_count.php       # Get cart item count
│   ├── 📄 get_favorites_count.php  # Get favorites count
│   ├── 📄 live_search.php          # Live search suggestions
│   ├── 📄 remove_from_cart.php     # Remove cart items
│   └── 📄 update_quantity.php      # Update cart quantities
├── 📁 assets/                      # Static Frontend Assets
│   ├── 📁 css/
│   │   ├── 📄 responsive.css       # Mobile and tablet responsive styles
│   │   └── 📄 style.css            # Main application styles
│   ├── 📁 images/
│   │   └── 📁 products/            # Product images directory
│   │       ├── 📄 ceramic_cups_set.jpg
│   │       ├── 📄 ceramic_vase_1.jpg
│   │       ├── 📄 earrings_1.jpg
│   │       ├── 📄 teddy_bear_1.jpg
│   │       ├── 📄 textile_bag_1.jpg
│   │       └── 📄 wooden_box_1.jpg
│   └── 📁 js/
│       └── 📄 main.js              # JavaScript functionality and AJAX
├── 📁 classes/                     # PHP Object-Oriented Classes
│   ├── 📄 CartPage.php             # Shopping cart page class
│   ├── 📄 Database.php             # Singleton database connection class
│   ├── 📄 Page.php                 # Base page class with common functionality
│   ├── 📄 Product.php              # Product model with CRUD operations
│   ├── 📄 ProductPage.php          # Product catalog page class
│   ├── 📄 Security.php             # Security and validation functions
│   └── 📄 Session.php              # Session management utilities
├── 📁 config/                      # Configuration Files
│   ├── 📄 config.php               # Application constants and settings
│   └── 📄 database.php             # Database connection settings
├── 📁 includes/                    # Shared Components
│   ├── 📄 footer.php               # Site footer with links
│   ├── 📄 functions.php            # Utility functions and helpers
│   ├── 📄 header.php               # Site header with navigation
│   └── 📄 navigation.php           # Main navigation menu
├── 📁 lang/                        # Internationalization
│   ├── 📄 en.php                   # English language translations
│   └── 📄 uk.php                   # Ukrainian language translations
└── 📁 sql/                         # Database Files
    ├── 📄 database.sql             # Complete database schema + test data
    └── 📄 production_database.sql  # Production-ready database export
```

### 📊 File Count Summary
- **Core Pages**: 7 PHP files (cart, checkout, contact, favorites, index, products, search)
- **AJAX Handlers**: 8 files for dynamic functionality
- **Classes**: 7 OOP class files for backend logic
- **Configuration**: 2 config files (database, app settings)
- **Includes**: 4 shared component files (header, footer, navigation, functions)
- **Languages**: 2 translation files (Ukrainian, English)
- **Assets**: 3 directories (CSS, JS, Images)
- **Product Images**: 6 sample product images
- **Database**: 2 SQL schema files
- **Total**: 35+ files organized in 7 main directories

## 🚀 Installation

### Requirements
- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **PHP**: 7.4+ (Recommended: 8.0+)
- **MySQL**: 5.7+ or MariaDB 10.2+

### Local Development Setup (XAMPP)

1. **Install XAMPP**
   - Download from [https://www.apachefriends.org](https://www.apachefriends.org)
   - Start Apache and MySQL services

2. **Clone the repository**
```bash
git clone https://github.com/yourusername/spl-final-project.git
# Move to XAMPP htdocs folder
mv spl-final-project C:/xampp/htdocs/handmade-shop
```

3. **Database Setup**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `handmade_shop`
   - Import: `sql/database.sql`

4. **Configuration**
Update `config/database.php` for local XAMPP:
```php
return [
    'host' => 'localhost',
    'database' => 'handmade_shop',
    'username' => 'root',
    'password' => '',  // Empty for XAMPP default
    'charset' => 'utf8mb4'
];
```

5. **Access Application**
   - Open: `http://localhost/handmade-shop/index.php`

## 🛡️ Security Features

- **CSRF Protection** - Unique tokens for all forms
- **XSS Prevention** - All output properly escaped
- **SQL Injection Protection** - PDO prepared statements only
- **Rate Limiting** - Request throttling per IP
- **Input Validation** - Comprehensive data sanitization
- **Secure Sessions** - Hardened session configuration

## 🌐 Multilingual Support

The application supports Ukrainian and English with:
- Dynamic language switching
- Translated interface elements
- Localized product information
- Language-specific URLs

## 📱 Responsive Design

- **Mobile-first** approach
- **CSS Grid/Flexbox** layouts
- **Touch-friendly** interfaces
- **Optimized images** for different screen sizes

## 🎨 Key Features Demo

### Shopping Cart
- Add/remove products with AJAX
- Quantity updates without page reload
- Session-based persistence
- Real-time total calculations

### Product Management
- Featured product highlighting
- Category-based filtering
- Search functionality
- Image galleries with fallbacks

### User Experience
- Smooth animations and transitions
- Loading states and feedback
- Error handling and validation
- Accessible design patterns

## 🔧 Technical Highlights

### Database Design
- Normalized schema with proper relationships
- Indexed columns for performance
- Support for multilingual content
- Order tracking and analytics

### Code Quality
- PSR-12 coding standards
- Object-oriented architecture
- Separation of concerns
- Comprehensive error handling

### Performance
- Optimized database queries
- Lazy loading for images
- Minimal JavaScript footprint
- CSS/JS asset optimization

## 📊 Database Schema

### Core Tables
- `products` - Product catalog with multilingual support
- `categories` - Product categorization
- `orders` - Customer orders and details
- `order_items` - Order line items
- `contact_messages` - Customer inquiries

## 🧪 Testing

The application has been tested for:
- ✅ Add/remove products from cart
- ✅ Checkout process completion  
- ✅ Search and filtering functionality
- ✅ Language switching
- ✅ Contact form submission
- ✅ Responsive design across devices
- ✅ Security vulnerability protection

## 📈 Production Deployment

Currently deployed on **InfinityFree** hosting:
- Live database with test data
- SSL certificate enabled
- Error logging configured
- Performance monitoring active

## 🎯 Learning Outcomes

This project demonstrates proficiency in:
- **Server-side scripting** with PHP
- **Database design** and optimization
- **Security implementation** in web applications
- **Responsive web design** principles
- **AJAX** and modern JavaScript
- **Internationalization** (i18n)
- **Object-oriented programming** concepts

## 📝 SPL Course Integration

This project fulfills all **Scripted Programming Language** course requirements:
- Comprehensive use of PHP scripting
- Server-side session management
- Database integration with PDO
- Form processing and validation
- File structure organization
- Security best practices

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/improvement`)
3. Commit changes (`git commit -m 'Add improvement'`)
4. Push to branch (`git push origin feature/improvement`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Author

**SPL Final Project 2025**
- Course: Scripted Programming Language
- Technologies: PHP, MySQL, JavaScript, CSS
- University: [Your University Name]

---

**Created with ❤️ for the SPL course final project**