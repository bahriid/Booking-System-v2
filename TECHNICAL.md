# MagShip B2B Booking - Technical Documentation

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Installation Guide](#installation-guide)
3. [Environment Configuration](#environment-configuration)
4. [SMTP Configuration](#smtp-configuration)
5. [Scheduled Tasks (Cron)](#scheduled-tasks-cron)
6. [Database Backup](#database-backup)
7. [Deployment Guide](#deployment-guide)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Server Requirements
| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 8.2+ | 8.3+ |
| MySQL | 8.0+ | 8.0+ |
| Composer | 2.0+ | Latest |
| Node.js | 18+ | 20+ (for asset compilation) |

### PHP Extensions Required
- BCMath
- Ctype
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO (MySQL driver)
- Tokenizer
- XML
- GD (for PDF generation)

### Recommended Server Specs
| Environment | CPU | RAM | Disk |
|-------------|-----|-----|------|
| Development | 1 core | 2GB | 10GB |
| Production | 2+ cores | 4GB+ | 50GB+ |

---

## Installation Guide

### 1. Clone Repository

```bash
git clone <repository-url> magship
cd magship
```

### 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

For development:
```bash
composer install
```

### 3. Install Node Dependencies (Optional - for asset compilation)

```bash
npm install
npm run build
```

### 4. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Environment

Edit `.env` file with your settings (see [Environment Configuration](#environment-configuration)).

### 6. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE magship CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# (Optional) Seed with demo data
php artisan db:seed
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 9. Verify Installation

```bash
php artisan about
```

---

## Environment Configuration

### Core Settings

```env
# Application
APP_NAME="MagShip B2B Booking"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magship
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Timezone
APP_TIMEZONE=Europe/Rome
```

### Locale Settings

```env
# Default application locale
APP_LOCALE=en

# Fallback locale
APP_FALLBACK_LOCALE=en

# Available locales (comma-separated)
APP_AVAILABLE_LOCALES=en,it
```

---

## SMTP Configuration

### Basic SMTP Setup

Configure email sending by editing your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Admin notification email (receives system notifications)
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

### Provider-Specific Configurations

#### Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password    # Use App Password, not regular password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note:** For Gmail, you must enable 2FA and create an App Password at https://myaccount.google.com/apppasswords

#### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=eu-west-1
```

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your_mailgun_key
MAILGUN_ENDPOINT=api.eu.mailgun.net  # For EU region
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

### Testing Email Configuration

```bash
# Send a test email
php artisan tinker
>>> Mail::raw('Test email from MagShip', function($message) {
>>>     $message->to('test@example.com')->subject('Test');
>>> });
```

### Email Events

The system sends emails for the following events:

| Event | Recipients | Template |
|-------|------------|----------|
| Booking Confirmed | Partner, Admin | `booking-confirmed.blade.php` |
| Overbooking Requested | Admin | `overbooking-requested.blade.php` |
| Overbooking Approved | Partner | `overbooking-approved.blade.php` |
| Overbooking Rejected | Partner | `overbooking-rejected.blade.php` |
| Overbooking Expired | Partner, Admin | `overbooking-expired.blade.php` |
| Booking Cancelled | Partner, Admin | `booking-cancelled.blade.php` |
| Booking Modified | Partner, Admin | `booking-modified.blade.php` |
| Departure Cancelled | Affected Partners | `departure-cancelled.blade.php` |

### Email Logs

All sent emails are logged in the `email_logs` table and viewable at:
- Admin Panel > System > Email Logs (`/admin/email-logs`)

---

## Scheduled Tasks (Cron)

### Laravel Scheduler Setup

Add the following cron entry to your server (run `crontab -e`):

```cron
* * * * * cd /path/to/magship && php artisan schedule:run >> /dev/null 2>&1
```

### Scheduled Commands

| Command | Schedule | Description |
|---------|----------|-------------|
| `overbooking:expire` | Every minute | Expires pending overbooking requests after 2 hours |
| `db:backup` | Daily at 02:00 | Creates MySQL database backup |

### Manual Execution

```bash
# Expire overbooking requests manually
php artisan overbooking:expire

# Create database backup manually
php artisan db:backup
```

### Viewing Scheduler Status

```bash
php artisan schedule:list
```

---

## Database Backup

### Automatic Backups

Backups run daily at 02:00 AM via the Laravel scheduler (requires cron setup).

### Backup Storage

Backups are stored in: `storage/app/backups/`

Filename format: `backup_YYYY-MM-DD_HHMMSS.sql`

### Manual Backup

```bash
php artisan db:backup
```

### Backup Retention

By default, backups are retained indefinitely. To implement retention:

```bash
# Delete backups older than 30 days
find /path/to/magship/storage/app/backups -name "*.sql" -mtime +30 -delete
```

Add this to cron for automatic cleanup:
```cron
0 3 * * * find /path/to/magship/storage/app/backups -name "*.sql" -mtime +30 -delete
```

### Backup Logs

Backup history is logged in the `backup_logs` table and viewable at:
- Admin Panel > System > Backup Logs (`/admin/backup-logs`)

### Restoring from Backup

```bash
mysql -u your_db_user -p magship < storage/app/backups/backup_2025-01-15_020000.sql
```

### Remote Backup (Recommended)

For production, copy backups to remote storage:

```bash
# Example: Sync to S3
aws s3 sync storage/app/backups s3://your-bucket/magship-backups/

# Example: SCP to remote server
scp storage/app/backups/*.sql user@backup-server:/backups/magship/
```

---

## Deployment Guide

### Production Deployment Checklist

1. **Environment**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize Application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

3. **Database Migration**
   ```bash
   php artisan migrate --force
   ```

4. **Clear Old Caches** (when updating)
   ```bash
   php artisan optimize:clear
   ```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/magship/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration (.htaccess)

The default Laravel `.htaccess` in `/public` handles most configurations. Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### SSL/HTTPS

For production, always use HTTPS. Use Let's Encrypt for free SSL:

```bash
sudo certbot --nginx -d yourdomain.com
```

### Queue Worker (Optional)

For better email performance, use queue workers:

```env
QUEUE_CONNECTION=database
```

```bash
# Create jobs table
php artisan queue:table
php artisan migrate

# Run queue worker
php artisan queue:work --daemon
```

Use Supervisor to keep the queue worker running:

```ini
[program:magship-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/magship/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/magship/storage/logs/worker.log
stopwaitsecs=3600
```

---

## Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 2. Class Not Found
```bash
composer dump-autoload
php artisan clear-compiled
```

#### 3. Session/Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 4. Emails Not Sending
```bash
# Test SMTP connection
php artisan tinker
>>> config('mail')

# Check email logs in database
>>> \App\Models\EmailLog::latest()->first()
```

#### 5. PDF Generation Issues
- Ensure GD extension is installed
- Check DomPDF cache permissions: `storage/app/dompdf`

#### 6. Scheduled Tasks Not Running
```bash
# Verify cron is running
crontab -l

# Test scheduler manually
php artisan schedule:run

# Check logs
tail -f storage/logs/laravel.log
```

### Debug Mode

**Never enable debug mode in production!**

For debugging locally:
```env
APP_DEBUG=true
```

### Log Files

| Log | Location | Purpose |
|-----|----------|---------|
| Laravel | `storage/logs/laravel.log` | Application errors |
| Queue | `storage/logs/worker.log` | Queue worker output |
| Nginx | `/var/log/nginx/error.log` | Web server errors |
| PHP-FPM | `/var/log/php8.3-fpm.log` | PHP errors |

### Health Check

Access `/` and verify the login page loads correctly.

Check database connection:
```bash
php artisan db:show
```

---

## Default User Accounts

After running `php artisan db:seed`, the following test accounts are available:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@magship.com | password |
| Driver | driver@magship.com | password |
| Partner (Hotel Paradiso) | paradiso@example.com | password |
| Partner (Grand Hotel) | grand@example.com | password |

**Important:** Change these passwords immediately in production!

---

## Support

For technical support:
1. Check this documentation
2. Review `storage/logs/laravel.log` for errors
3. Consult Laravel documentation: https://laravel.com/docs

---

*Last updated: December 2025*
