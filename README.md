# 🍕 SnackHaven - Complete Food Ordering System

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://javascript.com)

> A comprehensive web-based food ordering system built with PHP, MySQL, and modern web technologies. Features user authentication, dynamic menu management, shopping cart functionality, and administrative dashboard.

## 🌟 Key Features

### 🛍️ **Customer Features**
- **Modern User Interface**: Responsive design with smooth animations and intuitive navigation
- **User Authentication**: Secure registration, login, and email verification system
- **Dynamic Menu Browsing**: Category-based product filtering with pagination
- **Shopping Cart**: Real-time cart management with localStorage persistence
- **Checkout System**: Complete order processing with shipping address management
- **Order Tracking**: View order history and current order status
- **Profile Management**: Update personal information and addresses
- **Contact System**: Send inquiries through integrated contact form

### 👨‍💼 **Administrative Features**
- **Dashboard Analytics**: Comprehensive sales reports and customer insights
- **Product Management**: CRUD operations for menu items with image upload
- **Category Management**: Organize products into customizable categories
- **Order Management**: Track and update order statuses in real-time
- **Customer Management**: View customer profiles and order history
- **Message Center**: Respond to customer inquiries and feedback

### 🔧 **Technical Features**
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **RESTful API**: JSON-based API endpoints for dynamic content loading
- **Security**: Password hashing, input sanitization, and SQL injection prevention
- **Database Optimization**: Efficient queries with proper indexing
- **Session Management**: Secure session handling with role-based access
- **File Upload**: Image management with external hosting integration (ImgBB)
- **Email Integration**: SMTP email system for notifications and verification

## 🚀 Live Demo

> **Note**: This is a portfolio project demonstrating full-stack web development capabilities

## 📊 Project Architecture

```
snackhaven/
├── 📁 admincp/              # Administrative control panel
│   ├── 📄 index.php         # Dashboard with analytics
│   ├── 📄 products.php      # Product management
│   ├── 📄 orders.php        # Order management
│   └── 📄 users.php         # User management
├── 📁 api/                  # RESTful API endpoints
│   ├── 📄 get_products.php  # Product listing API
│   ├── 📄 search.php        # Product search API
│   └── 📄 get_districts.php # Location data API
├── 📁 auth/                 # Authentication system
│   ├── 📄 login.php         # User login
│   ├── 📄 register.php      # User registration
│   └── 📄 logout.php        # Session termination
├── 📁 assets/               # Static resources
│   ├── 📁 css/              # Stylesheets
│   ├── 📁 js/               # JavaScript files
│   └── 📁 images/           # Image assets
├── 📁 includes/             # Core utilities
│   ├── 📄 config.php        # Database configuration
│   ├── 📄 header.php        # Common header
│   └── 📄 footer.php        # Common footer
├── 📁 pages/                # Main application pages
│   ├── 📄 home.php          # Landing page
│   ├── 📄 menu.php          # Product catalog
│   ├── 📄 checkout.php      # Order processing
│   └── 📄 profile.php       # User dashboard
└── 📄 index.php             # Application router
```

## 🛠️ Technology Stack

### **Backend**
- **PHP 7.4+**: Server-side scripting with modern features
- **MySQL 8.0**: Relational database with optimized queries
- **PDO**: Database abstraction layer for security
- **RESTful APIs**: JSON-based data exchange

### **Frontend**
- **HTML5 & CSS3**: Semantic markup and modern styling
- **Bootstrap 5.3**: Responsive UI framework
- **JavaScript ES6+**: Dynamic client-side functionality
- **jQuery**: DOM manipulation and AJAX requests
- **Font Awesome**: Icon library for enhanced UX

### **Third-Party Services**
- **ImgBB API**: External image hosting service
- **SweetAlert2**: Enhanced user notifications
- **SMTP**: Email delivery system

## 📋 Database Schema

The application uses a well-structured MySQL database with the following key tables:

- **`users`**: User accounts and profiles
- **`products`**: Menu items and descriptions
- **`categories`**: Product categorization
- **`orders`**: Order information and status
- **`order_items`**: Detailed order contents
- **`contact_messages`**: Customer inquiries
- **`email_verifications`**: Account verification tokens

## 🚀 Installation & Setup

### **Prerequisites**
- XAMPP/WAMP/LAMP server environment
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Web browser with JavaScript enabled

### **Installation Steps**

1. **Clone the Repository**
   ```bash
   git clone https://github.com/zaikaman/snackhaven.git
   cd snackhaven
   ```

2. **Database Setup**
   ```bash
   # Import the database schema
   mysql -u root -p < snackhaven.sql
   ```

3. **Environment Configuration**
   ```bash
   # Create .env file in project root
   echo "DB_URL=mysql://username:password@host:port/database" > .env
   ```

4. **Configure Web Server**
   - Place project files in web server document root
   - Ensure proper file permissions (755 for directories, 644 for files)
   - Configure virtual host if needed

5. **Access Application**
   ```
   http://localhost/snackhaven
   ```

### **Development Setup**

1. **Local Development Server**
   ```bash
   php -S localhost:8000
   ```

2. **Database Seeding**
   - Import `snackhaven.sql` for sample data
   - Create admin account through registration

3. **API Testing**
   ```bash
   # Test API endpoints
   curl http://localhost/snackhaven/api/get_products.php
   ```

## 📱 Key Functionalities

### **User Experience Flow**
1. **Landing Page**: Attractive hero section with featured categories
2. **Menu Browsing**: Filter products by category with dynamic loading
3. **Product Details**: Comprehensive product information and reviews
4. **Shopping Cart**: Real-time cart updates with quantity management
5. **Checkout**: Secure order processing with address validation
6. **Order Confirmation**: Email notifications and order tracking

### **Administrative Workflow**
1. **Dashboard**: Real-time analytics and key performance indicators
2. **Inventory Management**: Add, edit, and remove products
3. **Order Processing**: Update order status and manage fulfillment
4. **Customer Support**: Respond to inquiries and manage user accounts

## 🔐 Security Features

- **Password Hashing**: Bcrypt encryption for user passwords
- **Input Validation**: Server-side validation and sanitization
- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: Output escaping and CSP headers
- **Session Security**: Secure session management with regeneration
- **CSRF Protection**: Token-based form validation

## 📈 Performance Optimizations

- **Database Indexing**: Optimized queries with proper indexes
- **Image Optimization**: External CDN for faster loading
- **Caching**: Browser caching for static assets
- **Minification**: Compressed CSS and JavaScript files
- **Lazy Loading**: Dynamic content loading for better UX

## 🧪 Testing & Quality Assurance

### **Code Quality**
- Clean, well-documented PHP code
- Consistent coding standards and conventions
- Error handling and logging mechanisms
- Input validation and security measures

### **Browser Compatibility**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### **Responsive Design**
- Mobile-first approach
- Tablet and desktop optimizations
- Cross-device compatibility testing

## 🚦 Project Status

✅ **Completed Features**
- User authentication and authorization
- Product catalog with search functionality
- Shopping cart and checkout system
- Administrative dashboard
- Order management system
- Contact form and messaging

🔄 **Potential Enhancements**
- Payment gateway integration (PayPal, Stripe)
- Real-time notifications with WebSockets
- Advanced analytics and reporting
- Mobile application development
- Multi-language support
- Inventory management system

## 👨‍💻 Developer Information

**Đinh Phúc Thịnh**
- 🌐 **LinkedIn**: [linkedin.com/in/đinh-phúc-thịnh-2561b5274](https://www.linkedin.com/in/%C4%91inh-ph%C3%BAc-th%E1%BB%8Bnh-2561b5274)
- 🐱 **GitHub**: [github.com/zaikaman](https://github.com/zaikaman)
- 📧 **Email**: [zaikaman123@gmail.com](mailto:zaikaman123@gmail.com)

## 📝 Project Highlights

This project demonstrates proficiency in:

- **Full-Stack Development**: End-to-end web application development
- **Database Design**: Normalized database schema with relationships
- **API Development**: RESTful services with JSON responses
- **Security Implementation**: Best practices for web application security
- **User Experience**: Intuitive interface design and responsive layouts
- **Code Organization**: Modular architecture with separation of concerns
- **Version Control**: Git workflow and project documentation

## 🤝 Contributing

This is a portfolio project showcasing web development skills. While not actively seeking contributions, feedback and suggestions are welcome!

## 📄 License

This project is created for educational and portfolio purposes. Please respect the intellectual property and provide appropriate attribution if referencing this work.

---

<div align="center">

**Built with ❤️ by Đinh Phúc Thịnh**

*Demonstrating modern web development practices and full-stack capabilities*

</div>