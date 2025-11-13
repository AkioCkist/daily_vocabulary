# Daily Vocabulary 🎯

A modern web application for daily vocabulary learning and management, built with Laravel, Vue.js, and Inertia.js. This app helps users discover new words, track their learning progress, and build their vocabulary through daily practice.

## ✨ Features

- **Daily Word Discovery**: Get a new word every day to expand your vocabulary
- **Personal Vocabulary Management**: Save, organize, and track your learned words
- **User Authentication**: Secure user registration and login system
- **Progress Tracking**: Monitor your vocabulary learning journey
- **Responsive Design**: Beautiful, modern UI that works on all devices
- **Email Subscriptions**: Optional daily word delivery to your inbox

## 🛠️ Tech Stack

### Backend
- **PHP 8.2+** - Modern PHP features and performance
- **Laravel 12** - Robust web application framework
- **Laravel Fortify** - Authentication scaffolding
- **Laravel Sanctum** - API authentication
- **Laravel Telescope** - Development debugging and monitoring

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Inertia.js** - Modern monolith architecture
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Fast build tool and development server

### Database
- **PostgreSQL** - Primary database (can be configured for other databases)
- **Eloquent ORM** - Database interactions and relationships

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **PHP 8.2** or higher
- **Composer** - PHP dependency manager
- **Node.js 18+** and **npm** - JavaScript runtime and package manager
- **PostgreSQL** - Database server (or your preferred database)
- **Git** - Version control

## 🚀 Quick Start

### 1. Clone the Repository
```bash
git clone https://github.com/AkioCkist/daily_vocabulary.git
cd daily_vocabulary
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy the environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database
Edit your `.env` file with your database credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=daily_vocabulary
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Seed Database (Optional)
```bash
php artisan db:seed
```

### 8. Build Frontend Assets
```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Start the Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your application!

## 🏗️ Architecture Overview

This application follows Laravel's clean architecture principles with a well-organized structure:

### Directory Structure
```
app/
├── Http/Controllers/     # HTTP request handlers
├── Models/              # Eloquent models
├── Services/            # Business logic layer
├── Repositories/        # Data access layer
│   ├── Interfaces/      # Repository contracts
│   └── Eloquent/        # Eloquent implementations
└── Providers/           # Service providers

resources/
├── js/
│   ├── Components/      # Vue.js components
│   └── Pages/          # Inertia.js pages
└── views/              # Blade templates

database/
├── migrations/         # Database schema
├── factories/          # Model factories
└── seeders/           # Database seeders
```

### Key Models
- **User**: User authentication and profile management
- **Word**: Vocabulary words with definitions and examples
- **UserWord**: User's personal vocabulary tracking
- **DailyWordHistory**: Daily word selection history
- **Subscription**: Email subscription management

### Services Layer
- **DailyWordService**: Manages daily word selection logic
- **UserVocabularyService**: Handles user vocabulary operations
- **SubscriptionService**: Manages email subscriptions

## 🧪 Testing

This project includes comprehensive unit and integration tests.

### Running Tests
```bash
# Run all tests
.\vendor\bin\phpunit

# Run specific test suites
.\vendor\bin\phpunit tests\Unit\Services
.\vendor\bin\phpunit tests\Unit\Repositories
.\vendor\bin\phpunit tests\Unit\Integration

# Run with coverage
.\vendor\bin\phpunit --coverage-html coverage
```

### Test Structure
- **Unit Tests**: Service and repository layer testing with mocked dependencies
- **Integration Tests**: End-to-end testing with real database interactions
- **Feature Tests**: HTTP request and response testing

## 🛠️ Development Workflow

### 1. Setup Development Environment
```bash
# Install dependencies
composer install
npm install

# Setup environment
copy .env.example .env
php artisan key:generate
php artisan migrate
```

### 2. Start Development Servers
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server (for hot reloading)
npm run dev
```

### 3. Code Quality Tools
```bash
# PHP code formatting
.\vendor\bin\pint

# Static analysis
.\vendor\bin\phpstan analyse

# Run tests
.\vendor\bin\phpunit
```

## 📝 Available Scripts

### PHP/Laravel Scripts
```bash
# Development server
php artisan serve

# Database operations
php artisan migrate
php artisan migrate:refresh --seed
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Generate resources
php artisan make:controller ControllerName
php artisan make:model ModelName
php artisan make:migration create_table_name
```

### JavaScript/Node Scripts
```bash
# Development with hot reloading
npm run dev

# Production build
npm run build

# Install new packages
npm install package-name
```

## 🔧 Configuration

### Database Configuration
The application supports multiple database drivers. Configure in `.env`:
```env
# PostgreSQL (recommended)
DB_CONNECTION=pgsql

# MySQL
DB_CONNECTION=mysql

# SQLite (for testing)
DB_CONNECTION=sqlite
```

### Mail Configuration
For email subscriptions, configure your mail driver:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🚀 Deployment

### Production Build
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables
Ensure these are set in your production environment:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards for PHP
- Use TypeScript for complex JavaScript functionality
- Write tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:
1. Check the existing [Issues](https://github.com/AkioCkist/daily_vocabulary/issues)
2. Create a new issue with detailed information
3. Include error messages and steps to reproduce

## 🙏 Acknowledgments

- Laravel community for the excellent framework
- Vue.js team for the reactive frontend framework
- Tailwind CSS for the utility-first CSS framework
- Inertia.js for modern monolith architecture

---

Happy vocabulary building! 📚✨