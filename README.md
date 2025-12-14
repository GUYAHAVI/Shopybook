# 🛍️ Shopybook

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

**Shopybook** is a comprehensive, AI-powered multi-tenant platform built with Laravel that empowers small and medium businesses in emerging markets to digitize their operations, manage inventory, process payments, and build a professional online presence—all from a single dashboard.

Designed specifically for African businesses, Shopybook integrates local payment solutions (M-Pesa, Paystack), SMS services, and AI-driven tools to help entrepreneurs grow their businesses with minimal technical expertise.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Demo](#-demo)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Support](#-support)

---

## ✨ Features

### 🏢 **Multi-Tenant Business Management**
- Create and manage multiple businesses under one account
- Secure business data isolation with tenant-based architecture
- Support for product-based, service-based, and hybrid business models
- 2FA authentication for enhanced security
- Comprehensive business profile management with AI-powered descriptions

### 🎨 **AI-Powered Logo Generator**
- Generate professional logos instantly with Claude AI integration
- Business name and tagline automatically included
- 6 customizable styles: Modern, Classic, Minimal, Bold, Playful, Corporate
- AI-generated taglines from business descriptions
- User-provided or auto-generated taglines (max 5 words)
- 4-tier fallback system for 99.9% success rate

### 🛒 **Point of Sale (POS) System**
- Intuitive checkout interface optimized for speed
- Barcode scanning support
- Multiple payment methods (Cash, Card, M-Pesa, Mobile Money)
- Real-time inventory updates
- Receipt generation and printing
- Discount and tax calculations
- Customer management integration

### 📦 **Inventory Management**
- Track products and services with detailed categorization
- Stock level monitoring with low-stock alerts
- Batch and serial number tracking
- Product conversions and unit management
- Stock receipts and supplier management
- Inventory valuation with multiple costing methods (FIFO, LIFO, Average)
- Product receiving and quality control workflows

### 👥 **Customer Management**
- Comprehensive customer database
- Purchase history and analytics
- Customer grouping and segmentation
- Contact import from multiple sources (Google Contacts, vCard, CSV, Excel)
- Customer loyalty tracking

### 📊 **Reports & Analytics**
- Sales reports (daily, weekly, monthly, custom)
- Profit & loss statements
- Inventory reports with valuation
- Product performance analysis
- Customer insights and trends
- Exportable reports (PDF, Excel)

### 💳 **Payment Integration**
- **M-Pesa STK Push**: Direct mobile money payments
- **Paystack**: Card payments and subscriptions
- **Cash & Card**: Traditional payment methods
- Subscription management (Premium & Enterprise plans)
- 14-day free trial for new businesses
- Automated payment notifications

### 📱 **SMS Integration**
- Bulk SMS campaigns via HostPinnacle
- Low stock alerts via SMS
- Customer notifications
- Transaction confirmations
- Automated reminders

### 🌐 **Website Builder**
- No-code website builder with AI assistance
- Multiple professional themes
- Custom page creation with drag-and-drop editor
- Responsive designs for mobile and desktop
- SEO optimization tools
- Domain configuration and management
- Public business website hosting

### 🤖 **AI-Powered Features (Claude AI)**
- Business description enhancement
- Marketing content generation
- Product descriptions optimization
- Logo design prompts
- Tagline generation
- Smart recommendations

### 📧 **Email & Communication**
- Automated email notifications
- Contact form integration
- Welcome emails for new customers
- Transaction receipts
- Low stock alerts
- Marketing campaign emails

### 📈 **Marketing Tools**
- Social media post scheduling
- Multi-platform publishing (Facebook, Instagram, Twitter/X, LinkedIn)
- AI content generation for posts
- Image generation for social media
- Analytics and engagement tracking
- Contact list management

### 🔄 **Returns & Refunds**
- Streamlined return processing
- Refund management
- Inventory adjustment tracking
- Return reason analytics

### ⚙️ **Settings & Customization**
- Business settings management
- User profile customization
- Payment gateway configuration
- Tax and discount settings
- Email template customization
- Notification preferences

### 🔐 **Security Features**
- Two-Factor Authentication (2FA)
- Email verification for sensitive operations
- Secure business deletion with OTP
- CSRF protection
- Session management
- Role-based access control (planned)

### 🌍 **Progressive Web App (PWA)**
- Install as mobile app
- Offline capabilities
- Push notifications
- Fast loading times
- App-like experience

---

## 🛠️ Tech Stack

### **Backend**
- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Cache**: Database/Redis
- **Queue**: Database/Redis
- **Session**: Database
- **File Storage**: Local/S3 (configurable)

### **Frontend**
- **Templating**: Blade
- **CSS Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.4, Boxicons
- **JavaScript**: Vanilla JS, Alpine.js (components)
- **Charts**: Chart.js
- **Rich Text**: TinyMCE/CKEditor

### **Third-Party Integrations**
- **AI**: Claude API (Anthropic) - Sonnet 4
- **Image Generation**: Pollinations.AI, DiceBear, UI Avatars
- **Payments**: M-Pesa (Daraja API), Paystack
- **SMS**: HostPinnacle
- **Social Media**: Facebook Graph API, Instagram API, Twitter API
- **Email**: SMTP (configurable), SendGrid/Mailgun support
- **OCR**: Google Vision API (document scanning)

### **DevOps & Tools**
- **Version Control**: Git, GitHub
- **Deployment**: cPanel, Shared Hosting, VPS
- **Process Manager**: PM2 (for queues)
- **Monitoring**: Laravel Telescope (development)
- **Package Manager**: Composer, NPM

---

## 🎥 Demo

**Live Demo**: [https://shopybook.com](https://shopybook.com)

### Test Credentials
```
Email: demo@shopybook.com
Password: demo123
```

### Screenshots

| Dashboard | POS System | Reports |
|-----------|------------|---------|
| ![Dashboard](docs/screenshots/dashboard.png) | ![POS](docs/screenshots/pos.png) | ![Reports](docs/screenshots/reports.png) |

| Logo Generator | Website Builder | Marketing Tools |
|----------------|-----------------|-----------------|
| ![Logo](docs/screenshots/logo-gen.png) | ![Website](docs/screenshots/website.png) | ![Marketing](docs/screenshots/marketing.png) |

---

## 📥 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18.x & NPM
- MySQL >= 8.0
- Apache/Nginx web server

### Step 1: Clone the Repository

```bash
git clone https://github.com/GUYAHAVI/Shopybook.git
cd Shopybook
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup

```bash
# Create database (MySQL)
mysql -u root -p
CREATE DATABASE shopybook;
EXIT;

# Update .env file with database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopybook
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### Step 5: Storage Setup

```bash
# Create symbolic link for storage
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### Step 6: Build Assets

```bash
# Compile assets
npm run build

# Or for development with hot reload
npm run dev
```

### Step 7: Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Start queue worker (in separate terminal)
php artisan queue:work

# Access application at: http://localhost:8000
```

---

## ⚙️ Configuration

### Required API Keys

Add these to your `.env` file:

#### Claude AI (Required for AI features)
```env
CLAUDE_API_KEY=your_claude_api_key_here
```
Get your API key from: [https://console.anthropic.com/](https://console.anthropic.com/)

#### M-Pesa (Optional - for Kenya payments)
```env
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_PASSKEY=your_passkey
MPESA_SHORTCODE=your_shortcode
MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/callback
```

#### Paystack (Optional - for card payments)
```env
PAYSTACK_PUBLIC_KEY=pk_live_your_public_key
PAYSTACK_SECRET_KEY=sk_live_your_secret_key
PAYSTACK_WEBHOOK_URL=https://yourdomain.com/subscription/paystack/webhook
```

#### SMS Integration (Optional)
```env
HOSTPINNACLE_API_KEY=your_api_key
HOSTPINNACLE_SENDER_ID=SHOPYBOOK
```

#### Email Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@shopybook.com
MAIL_FROM_NAME="Shopybook"
```

### Session Configuration

```env
SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_DOMAIN=.yourdomain.com
```

### Cache & Queue

```env
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Application Settings

```env
APP_NAME=Shopybook
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Africa/Nairobi
```

---

## 📖 Usage

### Creating Your First Business

1. **Register an Account**
   - Visit the registration page
   - Provide email, password, and basic info
   - Verify your email address

2. **Create Business Profile**
   - Choose business type (Product/Service/Hybrid)
   - Enter business name and description
   - Generate or upload logo
   - Set contact information

3. **Generate AI Logo (Optional)**
   - Click "Generate with AI"
   - Enter optional tagline or let AI create one
   - Choose logo style
   - Download and apply

4. **Setup Inventory**
   - Navigate to Products/Services
   - Add your first items
   - Set pricing and stock levels
   - Configure categories

5. **Start Selling**
   - Open POS system
   - Add items to cart
   - Process payment
   - Print receipt

### Dashboard Overview

The main dashboard provides:
- **Quick Stats**: Today's sales, orders, revenue, customers
- **Sales Chart**: Visual representation of sales trends
- **Recent Orders**: Latest transactions
- **Low Stock Alerts**: Items needing restocking
- **Quick Actions**: Access to frequently used features

### Using the POS System

1. Click "POS" from the main menu
2. Search/scan products to add to cart
3. Adjust quantities as needed
4. Apply discounts if applicable
5. Select payment method
6. Complete transaction
7. Print/email receipt

### Managing Inventory

- **Add Products**: Products > Add New
- **Update Stock**: Edit product and adjust quantity
- **Receive Stock**: Stock Receipts > New Receipt
- **Transfer Stock**: Between locations (if applicable)
- **View Reports**: Inventory > Reports

### Marketing Features

1. **Create Social Media Posts**
   - Marketing > Social Media
   - Connect your accounts
   - Create post with AI assistance
   - Schedule or publish immediately

2. **Manage Contacts**
   - Marketing > Contacts
   - Import from Google/CSV/vCard
   - Create groups
   - Send bulk SMS/Email

---

## 📚 API Documentation

API documentation is available at `/api/documentation` when the application is running in development mode.

### Authentication

All API requests require authentication using Laravel Sanctum tokens.

```bash
# Get authentication token
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}

# Use token in requests
Authorization: Bearer {your-token}
```

### Example Endpoints

```bash
# Get all products
GET /api/products

# Create new product
POST /api/products
{
  "name": "Product Name",
  "price": 100.00,
  "stock": 50
}

# Process sale
POST /api/sales
{
  "items": [...],
  "payment_method": "cash",
  "amount_paid": 500.00
}
```

Full API documentation coming soon!

---

## 🤝 Contributing

We welcome contributions from the community! Here's how you can help:

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/GUYAHAVI/Shopybook/issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots (if applicable)
   - Environment details

### Suggesting Features

1. Open a new issue with the `enhancement` label
2. Describe the feature and its benefits
3. Provide examples or mockups if possible

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Make your changes
4. Write/update tests if applicable
5. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
6. Push to the branch (`git push origin feature/AmazingFeature`)
7. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation as needed
- Test your changes thoroughly

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Laravel**: The PHP framework for web artisans
- **Claude AI**: Advanced AI assistance by Anthropic
- **Bootstrap**: Responsive CSS framework
- **Font Awesome**: Icon library
- **M-Pesa**: Mobile payment integration
- **Paystack**: Payment processing
- **HostPinnacle**: SMS services

---

## 📞 Support

### Get Help

- **Documentation**: [https://docs.shopybook.com](https://docs.shopybook.com)
- **Email**: support@shopybook.com
- **Issues**: [GitHub Issues](https://github.com/GUYAHAVI/Shopybook/issues)
- **Discussions**: [GitHub Discussions](https://github.com/GUYAHAVI/Shopybook/discussions)

### Community

- **Twitter**: [@Shopybook](https://twitter.com/shopybook)
- **Facebook**: [ShopybookApp](https://facebook.com/shopybookapp)
- **LinkedIn**: [Shopybook](https://linkedin.com/company/shopybook)

### Enterprise Support

For enterprise support, custom development, or partnership inquiries, contact: enterprise@shopybook.com

---

## 🗺️ Roadmap

### Q1 2026
- [ ] Mobile apps (iOS & Android)
- [ ] Multi-currency support
- [ ] Advanced role-based permissions
- [ ] API marketplace

### Q2 2026
- [ ] Accounting integration (QuickBooks, Xero)
- [ ] Multi-warehouse management
- [ ] Advanced reporting with custom dashboards
- [ ] WhatsApp Business integration

### Q3 2026
- [ ] E-commerce marketplace
- [ ] Vendor/supplier portal
- [ ] Advanced analytics with AI insights
- [ ] International payment gateways

### Q4 2026
- [ ] Enterprise features
- [ ] White-label solution
- [ ] Franchise management
- [ ] Advanced automation workflows

---

## 👨‍💻 Author

**Harvey Guyahavi**
- GitHub: [@GUYAHAVI](https://github.com/GUYAHAVI)
- Email: harvey@shopybook.com

---

## ⭐ Star History

If you find Shopybook useful, please consider giving it a star! ⭐

[![Star History Chart](https://api.star-history.com/svg?repos=GUYAHAVI/Shopybook&type=Date)](https://star-history.com/#GUYAHAVI/Shopybook&Date)

---

<div align="center">

**Made with ❤️ for African Entrepreneurs**

</div>
