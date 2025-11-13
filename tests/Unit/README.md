# Unit Tests for Daily Vocabulary App

This directory contains comprehensive unit tests for the Services and Repositories in the Daily Vocabulary application.

## Setup

### Database Configuration

The tests are configured to use PostgreSQL. Make sure you have:

1. PostgreSQL installed and running
2. A test database created: `daily_vocabulary_test`
3. PostgreSQL user with access to the test database

### Test Database Setup

```sql
-- Connect to PostgreSQL as postgres user
CREATE DATABASE daily_vocabulary_test;
CREATE USER test_user WITH PASSWORD 'test_password';
GRANT ALL PRIVILEGES ON DATABASE daily_vocabulary_test TO test_user;
```

### Environment Variables

Update your `phpunit.xml` or create a `.env.testing` file with:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=daily_vocabulary_test
DB_USERNAME=your_postgres_user
DB_PASSWORD=your_postgres_password
```

## Running Tests

### All Unit Tests
```bash
.\vendor\bin\phpunit tests\Unit
```

### Service Tests Only
```bash
.\vendor\bin\phpunit tests\Unit\Services
```

### Repository Tests Only
```bash
.\vendor\bin\phpunit tests\Unit\Repositories
```

### Integration Tests
```bash
.\vendor\bin\phpunit tests\Unit\Integration
```

### Specific Test File
```bash
.\vendor\bin\phpunit tests\Unit\Services\DailyWordServiceTest.php
```

## Test Structure

### Services Tests (`tests/Unit/Services/`)
- **DailyWordServiceTest.php**: Tests business logic for daily word selection
- **SubscriptionServiceTest.php**: Tests subscription management with mocked repositories
- **UserVocabularyServiceTest.php**: Tests user vocabulary management

### Repository Tests (`tests/Unit/Repositories/`)
- **EloquentWordRepositoryTest.php**: Tests word data access layer
- **EloquentUserWordRepositoryTest.php**: Tests user-word relationship data access
- **EloquentSubscriptionRepositoryTest.php**: Tests subscription data access

### Integration Tests (`tests/Unit/Integration/`)
- **ServiceRepositoryIntegrationTest.php**: Tests services working with real repositories

## Test Features

- ✅ **Mocked Dependencies**: Service tests use mocked repositories
- ✅ **Database Integration**: Repository tests use real PostgreSQL database
- ✅ **Model Factories**: Automated test data generation
- ✅ **Mail Testing**: Email functionality testing with Laravel's mail fake
- ✅ **Edge Cases**: Comprehensive coverage including error scenarios

## Troubleshooting

### PostgreSQL Driver Issues
If you get "could not find driver" errors:
```bash
# Windows (if using XAMPP/WAMP)
php -m | findstr pgsql

# Enable pgsql extension in php.ini
extension=pgsql
extension=pdo_pgsql
```

### Database Connection Issues
1. Verify PostgreSQL is running
2. Check database credentials in phpunit.xml
3. Ensure test database exists and user has permissions

### Memory Issues
For large test suites, increase PHP memory limit:
```bash
php -d memory_limit=512M .\vendor\bin\phpunit tests\Unit
```

## Coverage

Run tests with coverage (requires Xdebug):
```bash
.\vendor\bin\phpunit tests\Unit --coverage-html coverage
```