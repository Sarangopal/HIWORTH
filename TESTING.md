# Testing Guide

This document describes the testing setup for the Hiworth application.

## Test Structure

### Feature Tests
Located in `tests/Feature/`:
- **UserApiTest.php** - Tests for User API endpoints
- **TaskApiTest.php** - Tests for Task API endpoints

### Browser Tests (Laravel Dusk)
Located in `tests/Browser/`:
- **UserManagementTest.php** - Browser tests for user management
- **TaskManagementTest.php** - Browser tests for task management

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Feature Tests Only
```bash
php artisan test --testsuite=Feature
```

### Run Browser Tests Only
```bash
php artisan test --testsuite=Browser
```

### Run Specific Test Class
```bash
php artisan test tests/Feature/UserApiTest.php
```

### Run Dusk Tests
```bash
php artisan dusk
```

## Test Coverage

### API Tests (Feature Tests)

#### User API Tests
- ✅ List all users
- ✅ Create user
- ✅ User creation validation (name, email, password required)
- ✅ Email validation
- ✅ Unique email validation
- ✅ Password minimum length validation
- ✅ Show user with tasks
- ✅ Update user
- ✅ Delete user

#### Task API Tests
- ✅ List all tasks
- ✅ Filter tasks by user
- ✅ Create task
- ✅ Task creation validation (user_id, title required)
- ✅ Valid user_id validation
- ✅ Status validation
- ✅ Show task
- ✅ Update task
- ✅ Delete task

### Browser Tests (Dusk)

#### User Management Tests
- ✅ Create user through UI
- ✅ Validation errors display
- ✅ Email validation
- ✅ List users
- ✅ View user details

#### Task Management Tests
- ✅ Create task through UI
- ✅ Validation errors display
- ✅ View tasks for specific user
- ✅ Update task status
- ✅ Delete task
- ✅ Filter tasks by user
- ✅ Navigate between pages

## Setup Requirements

### Database Configuration

For testing, you need to configure a test database. Update `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="hiworth_test"/>
```

Or create a `.env.testing` file:
```
DB_CONNECTION=mysql
DB_DATABASE=hiworth_test
DB_USERNAME=root
DB_PASSWORD=
```

### Create Test Database

```bash
# Create test database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS hiworth_test;"

# Or using Laravel
php artisan db:create hiworth_test
```

### Laravel Dusk Setup

Dusk is already installed. To run browser tests:

1. Make sure Chrome/Chromium is installed
2. ChromeDriver is automatically downloaded by Dusk
3. Run tests: `php artisan dusk`

### Running Tests in CI/CD

For continuous integration, you may want to:

1. Use SQLite for faster tests (if available):
   ```xml
   <env name="DB_CONNECTION" value="sqlite"/>
   <env name="DB_DATABASE" value=":memory:"/>
   ```

2. Or use a separate MySQL test database

3. Run migrations before tests:
   ```bash
   php artisan migrate --env=testing
   ```

## Test Data

Tests use Laravel Factories:
- `UserFactory` - Creates test users
- `TaskFactory` - Creates test tasks

Factories are located in `database/factories/`.

## Notes

- All tests use `RefreshDatabase` trait to ensure clean state
- Browser tests use `DatabaseMigrations` trait
- Tests simulate real user interactions (form submission, button clicks, etc.)
- Validation errors are tested both in API and browser tests

## Troubleshooting

### "could not find driver" Error
- Ensure MySQL PDO extension is enabled in PHP
- Or switch to SQLite in `phpunit.xml` if SQLite extension is available

### Dusk Tests Failing
- Ensure Chrome/Chromium is installed
- Check ChromeDriver version compatibility
- Run `php artisan dusk:install` to reinstall ChromeDriver

### Database Connection Issues
- Verify test database exists
- Check database credentials in `phpunit.xml` or `.env.testing`
- Ensure database user has proper permissions

