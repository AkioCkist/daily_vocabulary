# Daily Vocabulary 🎯

A comprehensive web application for vocabulary learning and management, built with Laravel, Vue.js, and Inertia.js. This app provides an engaging, gamified experience for users to discover new words, practice with interactive flashcards, track their learning progress, and build their vocabulary through multiple learning modes.

## ✨ Core Features

### 🏠 Home Dashboard
- **Daily Word Discovery**: Get a curated word every day to expand your vocabulary
- **Progress Overview**: Visual statistics showing your learning journey
- **Day Range Analytics**: Track your performance over different time periods (7, 30, 90 days, all time)
- **Memory Report**: View frequently forgotten and well-remembered words
- **Quick Actions**: Fast access to learning, review, and flashcard practice

### 📚 Flashcard System
A powerful, interactive flashcard system with multiple modes and customization options:

#### Flashcard Modes
- **Standard Mode**: Traditional flashcard with word → definition reveal
- **Fill-in-the-Blank Mode**: Type the word based on definition and example
- **Mixed Mode**: Randomly alternates between standard and fill-blank for variety

#### Flashcard Features
- **Multiple Practice Modes**:
  - **Quick Practice**: Random words for fast sessions
  - **Review Mode**: Focus on words you've struggled with
  - **Topic-Based**: Practice specific topics
  - **Advanced Mode**: Customize by CEFR level, difficulty, mastery status
  - **Saved Sessions**: Resume previously saved flashcard sets

- **Interactive Elements**:
  - Progressive hint system (reveals word letter by letter)
  - Audio pronunciation with text-to-speech
  - Real-time progress tracking
  - Instant feedback on answers
  - Topic management during practice

- **Performance Optimizations**:
  - Fast card transitions (100ms auto-advance)
  - Smooth animations (0.3-0.6s)
  - Cached user data for quick loading
  - Optimized hint animations

- **Session Management**:
  - Save practice sessions for later review
  - Custom session naming
  - Shuffle and reorder flashcards
  - Session statistics and completion tracking

### 📖 Learning System
Structured learning experience for discovering new vocabulary:

- **Guided Learning Sessions**: Step-by-step word introduction
- **Word Details**: Comprehensive information including:
  - Word pronunciation and audio
  - Multiple definitions
  - Example sentences
  - Part of speech
  - CEFR level classification
  - Topic categorization
- **Progress Tracking**: Mark words as learned or for review
- **Customizable Sessions**: Choose word count and difficulty

### 🔄 Review System
Spaced repetition and targeted review:

- **Mistake-Based Review**: Focus on words you've gotten wrong
- **Mastery Tracking**: Monitor which words you've mastered
- **Forgotten Words**: Special attention to frequently forgotten vocabulary
- **Review Statistics**: Track improvement over time

### 🧪 Testing System
Assess your vocabulary knowledge:

- **Daily Tests**: Regular vocabulary assessments
- **Custom Tests**: Create tests with specific parameters
- **Multiple Question Types**: Various formats to test comprehension
- **Test History**: Review past performance
- **Detailed Results**: See which words you got right/wrong

### 🏷️ Topic Management
Organize vocabulary by themes:

- **System Topics**: Pre-defined categories (Business, Technology, Travel, etc.)
- **Custom Topics**: Create your own topic collections
- **Word Collections**: Add words to multiple topics
- **Topic-Based Practice**: Study specific themes
- **Topic Statistics**: Track progress per topic

### 💾 Saved Sessions
Reusable flashcard collections:

- **Save Practice Sets**: Preserve flashcard combinations for future use
- **Session Library**: Browse and manage all saved sessions
- **Quick Review**: Instantly start practicing saved sessions
- **Session Metadata**: Track creation date, word count, and topic
- **Shuffle Option**: Randomize word order on each practice

### 📊 Progress Analytics
Comprehensive tracking and statistics:

- **Learning Metrics**:
  - Total words learned
  - Words in review
  - Mastered words
  - Current streak

- **Performance Metrics**:
  - Accuracy rates
  - Response times
  - Hint usage statistics
  - Difficulty scores per word

- **Time-Based Analytics**:
  - Daily/weekly/monthly progress
  - Study time tracking
  - Consistency metrics

### 👤 User Management
- **Secure Authentication**: Email verification and password reset
- **Profile Management**: Update personal information
- **Email Subscriptions**: Optional daily word delivery
- **API Token Management**: Generate tokens for API access
- **Subscription Preferences**: Customize notification settings

### 🎨 User Experience
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile
- **Dark Mode Support**: Easy on the eyes for extended study sessions
- **Smooth Animations**: Polished transitions and interactions
- **Keyboard Shortcuts**: Efficient navigation for power users
- **Accessibility**: ARIA labels and semantic HTML

## 🛠️ Tech Stack

### Backend
- **PHP 8.2+** - Modern PHP features and performance
- **Laravel 12** - Robust web application framework
- **Laravel Fortify** - Authentication scaffolding
- **Laravel Sanctum** - API authentication
- **Laravel Telescope** - Development debugging and monitoring
- **Redis Cache** - High-performance caching layer

### Frontend
- **Vue.js 3** - Progressive JavaScript framework with Composition API
- **Inertia.js** - Modern monolith architecture
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Fast build tool and development server
- **Heroicons** - Beautiful hand-crafted SVG icons

### Database
- **PostgreSQL** - Primary database with advanced features
- **Eloquent ORM** - Elegant database interactions
- **Database Indexing** - Optimized query performance

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **PHP 8.2** or higher
- **Composer** - PHP dependency manager
- **Node.js 18+** and **npm** - JavaScript runtime and package manager
- **PostgreSQL 14+** - Database server
- **Redis** (optional) - For caching and session management
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

### 6. Configure Cache (Optional but Recommended)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 7. Run Database Migrations
```bash
php artisan migrate
```

### 8. Seed Database
```bash
# Seed with sample vocabulary data
php artisan db:seed

# Or seed specific seeders
php artisan db:seed --class=WordSeeder
php artisan db:seed --class=TopicSeeder
```

### 9. Build Frontend Assets
```bash
# For development with hot reload
npm run dev

# For production
npm run build
```

### 10. Start the Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your application!

## 🏗️ Architecture Overview

This application follows Laravel's clean architecture principles with a well-organized structure:

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── FlashcardController.php      # Flashcard system
│   │   ├── LearningController.php       # Learning sessions
│   │   ├── ReviewController.php         # Review system
│   │   ├── TestController.php           # Testing system
│   │   ├── TopicController.php          # Topic management
│   │   ├── SavedSessionController.php   # Saved sessions
│   │   └── HomeController.php           # Dashboard
│   └── Middleware/                      # Custom middleware
├── Models/
│   ├── User.php                         # User model
│   ├── Word.php                         # Vocabulary words
│   ├── UserWord.php                     # User progress tracking
│   ├── Topic.php                        # Topic categorization
│   ├── FlashcardAttempt.php            # Flashcard practice records
│   ├── SavedSession.php                # Saved flashcard sessions
│   ├── TestAttempt.php                 # Test results
│   └── Subscription.php                # Email subscriptions
├── Services/
│   ├── DashboardService.php            # Dashboard analytics
│   ├── UserProgressService.php         # Progress tracking
│   ├── TopicService.php                # Topic operations
│   ├── LearningService.php             # Learning logic
│   ├── ReviewService.php               # Review logic
│   ├── TestService.php                 # Testing logic
│   └── SubscriptionService.php         # Email management
└── Providers/                          # Service providers

resources/
├── js/
│   ├── Components/
│   │   ├── Flashcard/                  # Flashcard components
│   │   │   ├── StandardFlashcard.vue
│   │   │   ├── FillBlankFlashcard.vue
│   │   │   ├── ProgressBar.vue
│   │   │   └── SessionComplete.vue
│   │   ├── Dashboard/                  # Dashboard widgets
│   │   └── Common/                     # Shared components
│   └── Pages/
│       ├── Home.vue                    # Dashboard page
│       ├── Flashcards/                 # Flashcard pages
│       ├── Learning/                   # Learning pages
│       ├── Review/                     # Review pages
│       ├── Test/                       # Test pages
│       └── SavedSessions/              # Saved sessions pages
└── views/                              # Blade templates

database/
├── migrations/                         # Database schema
├── factories/                          # Model factories
└── seeders/                           # Database seeders
```

### Key Models & Relationships

#### User Model
- Has many UserWords (vocabulary progress)
- Has many FlashcardAttempts
- Has many TestAttempts
- Has many Topics (custom topics)
- Has many SavedSessions

#### Word Model
- Belongs to many Users through UserWords
- Has many FlashcardAttempts
- Has many TestAttempts
- Belongs to Topic (system topic)

#### Topic Model
- Has many Words (system topics)
- Belongs to many Words through user_word_topics (custom topics)
- Belongs to User (for custom topics)

### Services Layer

#### DashboardService
- Aggregates user statistics
- Calculates progress metrics
- Generates analytics data
- Provides memory reports

#### UserProgressService
- Tracks word mastery
- Updates difficulty scores
- Manages learning streaks
- Records practice attempts

#### TopicService
- Manages system and user topics
- Handles topic CRUD operations
- Provides topic-based word filtering
- Caches topic data for performance

## 🎯 Performance Optimizations

### Backend Optimizations
- **Query Caching**: 5-minute cache for user topics and frequently accessed data
- **Database Indexing**: Optimized indexes on user_id, word_id, and topic relationships
- **Eager Loading**: Prevents N+1 query problems
- **Cache Invalidation**: Automatic cache clearing on data updates

### Frontend Optimizations
- **Fast Transitions**: 100ms auto-advance for responsive UX
- **Optimized Animations**: Balanced 0.3-0.6s CSS transitions
- **Lazy Loading**: Components loaded on demand
- **Debounced Inputs**: Prevents excessive API calls

### Route Optimizations
- Controller-based routes instead of closures
- Route caching in production
- Middleware optimization

## 🧪 Testing

This project includes comprehensive testing coverage.

### Running Tests
```bash
# Run all tests
.\\vendor\\bin\\phpunit

# Run specific test suites
.\\vendor\\bin\\phpunit tests\\Unit\\Services
.\\vendor\\bin\\phpunit tests\\Feature

# Run with coverage
.\\vendor\\bin\\phpunit --coverage-html coverage
```

### Test Structure
- **Unit Tests**: Service and repository layer testing
- **Feature Tests**: HTTP request and response testing
- **Integration Tests**: End-to-end testing with database

## 🛠️ Development Workflow

### 1. Setup Development Environment
```bash
# Install dependencies
composer install
npm install

# Setup environment
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### 2. Start Development Servers
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server (for hot reloading)
npm run dev

# Terminal 3: Queue worker (for background jobs)
php artisan queue:work
```

### 3. Code Quality Tools
```bash
# PHP code formatting
.\\vendor\\bin\\pint

# Static analysis
.\\vendor\\bin\\phpstan analyse

# Run tests
.\\vendor\\bin\\phpunit
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
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

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

### Cache Configuration
For optimal performance, use Redis:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Mail Configuration
For email subscriptions and notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
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

# Run migrations
php artisan migrate --force
```

### Environment Variables
Ensure these are set in your production environment:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards for PHP
- Use Vue 3 Composition API for new components
- Write tests for new features
- Update documentation as needed
- Maintain consistent code formatting

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
- PostgreSQL for robust database features

---

**Happy vocabulary building! 📚✨**

*Built with ❤️ using Laravel, Vue.js, and modern web technologies*