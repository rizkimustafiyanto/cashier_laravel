# RS Unknown - Transaction & Voucher Management System

A modern web application for managing transactions and vouchers with real-time interactive UI built with **Laravel 12**, **Livewire 4**, and **PostgreSQL**.

## 🎯 Overview

RS Unknown adalah sistem terintegrasi untuk manajemen transaksi pelanggan dan voucher/diskon. Aplikasi ini menyediakan dashboard analytics, transaction tracking, dan voucher management dengan role-based access control.

### Key Features

- 🔐 **Secure Authentication** - Laravel Fortify dengan 2FA support
- 👥 **Role-Based Access** - Admin, Marketing, Cashier roles dengan permission management
- 💳 **Transaction Management** - Create, track, dan manage customer transactions
- 🎟️ **Voucher System** - Create, apply, dan track voucher/discount codes
- 📊 **Real-time Dashboard** - Interactive analytics dan summary
- 📱 **Interactive UI** - Real-time form validation dengan Livewire components
- 📝 **Activity Logging** - Audit trail untuk semua perubahan data
- 📄 **Invoice Generation** - Download invoice sebagai PDF
- ⏰ **Timezone Support** - User dapat set timezone preference

## 🚀 Quick Start

### Prerequisites

- PHP 8.3+
- PostgreSQL 12+
- Node.js 18+ & npm
- Composer

### Installation

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd rs_<name>
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database** (edit `.env`)
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=rs_<name>
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

5. **Create Database & Run Migrations**
   ```bash
   createdb rs_<name>
   php artisan migrate
   ```

6. **Build Frontend Assets**
   ```bash
   npm run build
   ```

7. **Start Development Server** (Open 2 terminals)
   
   Terminal 1:
   ```bash
   php artisan serve
   ```
   
   Terminal 2:
   ```bash
   npm run dev
   ```

8. **Access Application**
   - URL: `http://localhost:8000`
   - Default login at `/login`

For detailed installation guide, see [📖 INSTALLATION.md](docs/INSTALLATION.md)

## 📁 Project Structure

```
rs_<name>/
├── app/                          # Application source code
│   ├── Actions/                  # Reusable business logic
│   ├── Console/Commands/         # Artisan commands
│   ├── DTOs/                     # Data Transfer Objects
│   ├── Enums/                    # Type-safe enumerations
│   ├── Events/                   # Application events
│   ├── Exceptions/               # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/          # HTTP controllers
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form request validation
│   ├── Livewire/                 # Livewire interactive components
│   ├── Mail/                     # Mailable templates
│   ├── Models/                   # Eloquent models
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Repository pattern
│   ├── Rules/                    # Custom validation rules
│   ├── Services/                 # Business logic services
│   └── Support/                  # Helper classes & utilities
├── config/                       # Configuration files
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── docs/                         # Detailed documentation
│   ├── INSTALLATION.md           # Setup & installation guide
│   └── INFRASTRUCTURE.md         # Architecture & technical details
├── routes/                       # Route definitions
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript files
├── tests/                        # Test files
├── storage/                      # Storage directory (logs, cache)
├── public/                       # Publicly accessible files
└── README.md                     # This file
```

## 🔐 Authentication & Authorization

### Roles Available

| Role | Access Level | Primary Functions |
|------|--------------|------------------|
| **Admin** | Full | System management, all features |
| **Marketing** | High | Dashboard, voucher management |
| **Cashier** | Limited | Transaction creation & management |

### Authentication System

- **Method**: Session-based with Laravel Fortify
- **2FA Support**: Two-factor authentication via TOTP (Google Authenticator)
- **Session Duration**: 120 minutes (configurable)
- **Password**: Bcrypt hashed with 12 rounds

Learn more in [📖 INFRASTRUCTURE.md](docs/INFRASTRUCTURE.md#2-sistem-autentikasi)

## 📊 Main Features

### 1. Transaction Management
- Create new transactions dengan patient info
- Apply voucher untuk discount
- Track transaction status (Draft, Paid, Cancelled)
- Download invoice sebagai PDF
- Activity logging semua perubahan

**Access**: Cashier role

### 2. Voucher Management
- Create/edit/delete vouchers
- Support percentage & fixed amount discounts
- Set date range aktivasi
- Auto-deactivate expired vouchers
- Track voucher usage

**Access**: Marketing role

### 3. Dashboard Analytics
- Transaction summary & statistics
- Revenue tracking
- Active vouchers count
- Real-time charts dengan ApexCharts
- Role-specific dashboard views

**Access**: Marketing & Cashier

### 4. User Management
- Profile management
- Password change
- 2FA setup
- Timezone preference

**Access**: All authenticated users

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.56
- **Authentication**: Laravel Fortify
- **Authorization**: Spatie Permission
- **Database**: PostgreSQL
- **Activity Logging**: Spatie Activity Log
- **PDF Generation**: Barryvdh DomPDF

### Frontend
- **UI Components**: Livewire 4.1 + Flux
- **Styling**: Tailwind CSS 4.0
- **Charts**: ApexCharts
- **Build Tool**: Vite
- **Other**: Alpine.js, Tom Select

### Development & Testing
- **Testing**: Pest 4.7
- **Code Quality**: Pint (PHP code fixer)
- **Debugging**: Laravel Debugbar
- **Database Testing**: Pest with Laravel plugin

## 📚 Detailed Documentation

### For Installation & Setup
See [📖 INSTALLATION.md](docs/INSTALLATION.md) for:
- Complete setup instructions
- Database configuration
- Troubleshooting guide
- Environment variables

### For Architecture & Development
See [📖 INFRASTRUCTURE.md](docs/INFRASTRUCTURE.md) for:
- System architecture overview
- Authentication system details
- Database schema
- Request lifecycle
- Design patterns used
- Common workflows

## 🔄 Workflows

### Creating a Transaction

1. Navigate to **Transactions → Create**
2. Fill patient information
3. Add items (procedures/services)
4. Apply voucher code (optional)
5. Review discount calculation
6. Submit transaction
7. Download invoice

### Creating a Voucher

1. Navigate to **Vouchers → Create**
2. Enter voucher details
3. Select discount type (Percentage/Fixed)
4. Set activation date range
5. Save voucher
6. System auto-deactivates after end date

### Setting Timezone

1. Go to **Profile**
2. Select your preferred timezone
3. Save changes
4. All timestamps akan adjusted berdasarkan timezone

## 🧪 Testing

Run automated tests:

```bash
# Run all tests
./vendor/bin/pest

# Run specific test file
./vendor/bin/pest tests/Feature/TransactionTest.php

# Run with coverage
./vendor/bin/pest --coverage
```

## 📋 Available Commands

### Artisan Commands

```bash
# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create admin user via tinker
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')])
>>> $user->assignRole('admin')

# Deactivate expired vouchers
php artisan voucher:deactivate-expired

# Send daily transaction report
php artisan report:daily-transactions

# Clear all caches
php artisan cache:clear

# Optimize application
php artisan optimize
```

### Composer Scripts

```bash
# Setup development environment
composer setup

# Run tests
composer test

# Run code fixer
composer pint

# Check code quality
composer lint
```

## 🔐 Security

- ✅ Password hashing with Bcrypt (12 rounds)
- ✅ CSRF protection on all forms
- ✅ SQL injection prevention via Eloquent ORM
- ✅ Two-factor authentication support
- ✅ Role-based access control
- ✅ Activity logging & audit trail
- ✅ Timezone-aware operations
- ✅ Session-based authentication

## 📧 Email Configuration

Configure email in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=no-reply@rs<name>.com
```

## 🔄 Queue & Scheduling

Project supports background jobs via database queue:

```bash
# Start queue worker
php artisan queue:work
```

Scheduled commands run daily:
- `report:daily-transactions` - 01:00 AM
- `voucher:deactivate-expired` - 00:00 AM

## 📦 Dependencies

### Core Dependencies
- laravel/framework: ^12.56
- laravel/fortify: ^1.34
- livewire/livewire: ^4.1
- livewire/flux: ^2.13.1
- spatie/laravel-permission: ^7.4
- spatie/laravel-activitylog: ^5.0

### Development Dependencies
- pestphp/pest: ^4.7
- laravel/debugbar: ^4.2
- barryvdh/laravel-pint: ^1.29

See `composer.json` & `package.json` for complete list.

## 🚨 Troubleshooting

### Database Connection Error
- Verify PostgreSQL is running
- Check database credentials in `.env`
- Ensure database exists

### Permission Denied Storage
```bash
chmod -R 775 storage bootstrap/cache
```

### Frontend Assets Not Loading
```bash
npm run build
```

### Memory Limit Issues
```bash
php -d memory_limit=-1 artisan migrate
```

For more troubleshooting, see [📖 INSTALLATION.md](docs/INSTALLATION.md#troubleshooting)

## 📞 Support

For detailed information about:
- **Setup & Installation** → [INSTALLATION.md](docs/INSTALLATION.md)
- **Architecture & Code** → [INFRASTRUCTURE.md](docs/INFRASTRUCTURE.md)
- **API & Endpoints** → See route definitions in [routes/web.php](routes/web.php)

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Commit messages should be clear and descriptive

## 📅 Version History

**Current Version**: 1.0.0 (May 2026)

---

**Last Updated**: May 20, 2026

For additional information and troubleshooting, refer to the [📖 docs/](docs/) folder.
