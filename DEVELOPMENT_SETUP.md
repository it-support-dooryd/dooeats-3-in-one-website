# Dooeats Development Environment Setup

## Overview
This document describes the development environment setup for the Dooeats food delivery platform, which consists of three separate Laravel applications:

1. **Customer App** - Port 8000
2. **Admin App** - Port 8001  
3. **Restaurant App** - Port 8002

## System Requirements
- PHP 8.4.11
- Composer 2.8.11
- Node.js 22.19.0
- npm 10.9.3
- MySQL Database

## Fixed Issues

### 1. PHP 8.4 Compatibility
**Problem**: Laravel 10 and its dependencies were designed for PHP 8.1-8.3, causing numerous deprecation warnings in PHP 8.4 that were being output before headers, resulting in "Cannot modify header information - headers already sent" errors.

**Solution**: Added error reporting configuration to suppress deprecation warnings in all three apps:
```php
// Suppress PHP 8.4 deprecation warnings to prevent header issues
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
```

Files modified:
- `/public/index.php` (Customer App)
- `/admin/public/index.php` (Admin App)
- `/restaurant/public/index.php` (Restaurant App)

### 2. Cookie Setting in CLI Context
**Problem**: The `AppServiceProvider` was attempting to set cookies during CLI execution (artisan commands), which is not allowed and caused errors.

**Solution**: Wrapped cookie-setting code in a check to only execute during web requests:
```php
if (!app()->runningInConsole()) {
    setcookie('XSRF-TOKEN-AK', bin2hex(env('FIREBASE_APIKEY')), time() + 3600, "/");
    // ... other cookies
}
```

Files modified:
- `/app/Providers/AppServiceProvider.php` (Customer App)
- `/admin/app/Providers/AppServiceProvider.php` (Admin App)
- `/restaurant/app/Providers/AppServiceProvider.php` (Restaurant App)

## Running the Development Servers

### Start All Three Apps
```bash
# Customer App (Port 8000)
cd "/Users/enohmichael/Dooeats version 2.0/Web Apps"
php artisan serve --host=127.0.0.1 --port=8000

# Admin App (Port 8001)
cd "/Users/enohmichael/Dooeats version 2.0/Web Apps/admin"
php artisan serve --host=127.0.0.1 --port=8001

# Restaurant App (Port 8002)
cd "/Users/enohmichael/Dooeats version 2.0/Web Apps/restaurant"
php artisan serve --host=127.0.0.1 --port=8002
```

### Access the Applications
- **Customer App**: http://127.0.0.1:8000
- **Admin App**: http://127.0.0.1:8001
- **Restaurant App**: http://127.0.0.1:8002

## Environment Configuration

Each app has its own `.env` file with Firebase configuration:
- Firebase API Key
- Firebase Auth Domain
- Firebase Database URL
- Firebase Project ID
- Firebase Storage Bucket
- Firebase Messaging Sender ID
- Firebase App ID
- Firebase Measurement ID

These are automatically encoded and set as cookies for frontend JavaScript access.

## Database Configuration

All three apps share the same database configuration:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doorvylq_foodie_website_database
DB_USERNAME=doorvylq_foodie_website_database_user
DB_PASSWORD=FGiI9%-Tv~B^
```

## Notes

- The deprecation warnings are suppressed but not fixed at the source. For production, consider upgrading to Laravel 11 or downgrading to PHP 8.3.
- The vendor directories already exist with dependencies installed.
- npm dependencies may need to be installed if you plan to modify frontend assets.

## Troubleshooting

### If you see "headers already sent" errors:
1. Ensure the error_reporting line is present in all three `public/index.php` files
2. Check that no output (echo, print, whitespace) occurs before the first header is sent

### If artisan commands fail:
1. Verify the `runningInConsole()` check is in place in all AppServiceProviders
2. Check that no cookies are being set during CLI execution

### If the server won't start:
1. Check if the port is already in use: `lsof -i :8000`
2. Verify PHP version: `php -v`
3. Check Laravel logs in `storage/logs/laravel.log`
