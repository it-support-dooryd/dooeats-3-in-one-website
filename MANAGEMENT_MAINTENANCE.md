# Dooeats Management & Maintenance Guide

## Server Maintenance

### Daily Checks
- **Log Monitoring**: Check `storage/logs/laravel.log` for any critical errors.
- **Queue Status**: Ensure queue workers are running and processing jobs.
- **Disk Space**: Monitor server disk space usage.

### Weekly Tasks
- **Database Backup**: Perform a full database backup and verify integrity.
- **Cache Clearing**: Run `php artisan cache:clear` and `php artisan config:clear` if configuration changes were made.
- **Update Dependencies**: Check for minor security updates with `composer outdated`.

### Deployment Procedure
1. **Pull Latest Code**: `git pull origin main`
2. **Install Dependencies**: `composer install --no-dev --optimize-autoloader`
3. **Run Migrations**: `php artisan migrate --force`
4. **Clear/Cache Config**: `php artisan config:cache`
5. **Clear/Cache Routes**: `php artisan route:cache`
6. **Clear/Cache Views**: `php artisan view:cache`
7. **Restart Queues**: `php artisan queue:restart`

## Troubleshooting

### Common Issues
- **500 Server Error**: Check `storage/logs/laravel.log`. Ensure permissions on `storage` and `bootstrap/cache` are 775.
- **Page Expired (419)**: Usually a CSRF token issue. Clear browser cache or check session configuration.
- **Queue Jobs Stuck**: Restart the queue worker or check for failed jobs in the `failed_jobs` table.

### Emergency Contacts
- **Lead Developer**: [Name] - [Email]
- **Server Admin**: [Name] - [Email]
