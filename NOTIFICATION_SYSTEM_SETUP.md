# 📧 Notification System Setup Guide

## Overview
The MyTime notification system sends automated email and in-app notifications for project due dates with the following schedule:

### Notification Schedule
- **3 Days Before**: 2 reminders per day (Morning at 9 AM & Evening at 6 PM)
- **2 Days Before**: 1 moderate alert per day (9 AM)
- **1 Day Before**: 1 HIGH alert per day (9 AM)
- **Due Date**: 1 CRITICAL alert (9 AM)
- **Overdue**: Daily alerts (9 AM) until project is completed/cancelled

## System Requirements

### For Production Deployment (24/7 Online)

1. **Web Server**: Apache/Nginx
2. **PHP**: ^8.2
3. **Database**: MySQL/PostgreSQL (SQLite not recommended for production)
4. **Queue Worker**: Required for email processing
5. **Task Scheduler**: Cron (Linux) or Task Scheduler (Windows)
6. **Email Service**: SMTP server or service (Gmail, SendGrid, Mailgun, etc.)

## Setup Instructions

### 1. Configure Email Settings

Edit your `.env` file with your email provider settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mytime.com
MAIL_FROM_NAME="MyTime Notifications"

# Admin email for error notifications
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

#### Popular Email Services:

**Gmail:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```
*Note: Use App Password, not your regular password*

**SendGrid:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

**Mailgun:**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-api-key
```

### 2. Configure Queue System

The notification system uses Laravel queues for reliable email delivery.

In `.env`:
```env
QUEUE_CONNECTION=database
```

Run migration to create jobs table:
```bash
php artisan queue:table
php artisan migrate
```

### 3. Set Up Task Scheduler (CRITICAL for 24/7 Operation)

The scheduler runs the notification checks twice daily (9 AM and 6 PM).

#### On Linux/macOS:

Add this single cron entry (it runs every minute and Laravel decides what to execute):

```bash
# Open crontab editor
crontab -e

# Add this line:
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

#### On Windows Server:

1. Open **Task Scheduler**
2. Create a new task:
   - **Name**: Laravel Scheduler
   - **Trigger**: Daily, repeat every 1 minute indefinitely
   - **Action**: Start a program
     - Program: `C:\php\php.exe` (your PHP path)
     - Arguments: `artisan schedule:run`
     - Start in: `C:\path\to\mytime`

#### Alternative: Using Laravel Forge/Envoyer/Ploi
These services automatically configure the scheduler for you.

### 4. Start Queue Worker (Required for Production)

The queue worker processes email jobs in the background.

#### Development:
```bash
php artisan queue:work --tries=3
```

#### Production (Supervisor - Recommended):

Install Supervisor:
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

Create supervisor config `/etc/supervisor/conf.d/mytime-worker.conf`:
```ini
[program:mytime-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mytime/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasuser=false
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mytime/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mytime-worker:*
```

#### Production (Systemd):

Create service file `/etc/systemd/system/mytime-queue.service`:
```ini
[Unit]
Description=MyTime Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/mytime
ExecStart=/usr/bin/php /path/to/mytime/artisan queue:work database --sleep=3 --tries=3
Restart=always
RestartSec=5s

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable mytime-queue
sudo systemctl start mytime-queue
sudo systemctl status mytime-queue
```

### 5. Enable User Email Notifications

Users must have email notifications enabled in their profile. Update the `users` table:

```sql
-- Enable email notifications for all users
UPDATE users SET email_notifications = 1;

-- Enable project update notifications for team members
UPDATE users SET project_updates = 1;
```

Or users can enable it in their profile settings.

## Testing the System

### 1. Test Email Configuration
```bash
php artisan tinker

# Send test email
Mail::raw('Test email from MyTime', function($message) {
    $message->to('your-email@example.com')->subject('Test Email');
});
```

### 2. Manually Run Notification Check
```bash
php artisan projects:check-due-dates
```

Check the logs at `storage/logs/laravel.log` for output.

### 3. View Scheduled Tasks
```bash
php artisan schedule:list
```

You should see:
```
0 9 * * * php artisan projects:check-due-dates (morning-due-date-check)
0 18 * * * php artisan projects:check-due-dates (evening-due-date-check)
```

### 4. Test Scheduler
```bash
# Run scheduler manually (simulates cron running)
php artisan schedule:run
```

### 5. Monitor Queue
```bash
# Check queue status
php artisan queue:monitor database

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Deployment Checklist

- [ ] `.env` file configured with email settings
- [ ] Database migrated (`php artisan migrate`)
- [ ] Queue table created (`php artisan queue:table` + migrate)
- [ ] Cron job added (Linux) or Task Scheduler configured (Windows)
- [ ] Queue worker running (via Supervisor/Systemd)
- [ ] Email configuration tested
- [ ] Test project created with due dates (1, 2, 3 days from now)
- [ ] User email notifications enabled
- [ ] Logs monitoring set up (`storage/logs/laravel.log`)

## Monitoring & Maintenance

### Log Files to Monitor
```bash
# Application logs
tail -f storage/logs/laravel.log

# Queue worker logs (if using Supervisor)
tail -f storage/logs/worker.log

# Web server logs
tail -f /var/log/nginx/error.log  # or Apache logs
```

### Common Issues & Solutions

**Emails not sending:**
- Check queue worker is running: `ps aux | grep queue:work`
- Check failed jobs: `php artisan queue:failed`
- Verify email credentials in `.env`
- Check email provider rate limits

**Notifications not triggering:**
- Verify cron is running: `grep CRON /var/log/syslog`
- Check scheduler list: `php artisan schedule:list`
- Manually test: `php artisan projects:check-due-dates`
- Check project has valid `end_date` and status is not completed/cancelled

**Queue worker stopped:**
```bash
# Restart supervisor
sudo supervisorctl restart mytime-worker:*

# Or restart systemd service
sudo systemctl restart mytime-queue
```

## Production Server Examples

### Using Shared Hosting (cPanel/Plesk)

1. Use cPanel Cron Jobs interface
2. Set to run every minute: `* * * * *`
3. Command: `/usr/local/bin/php /home/username/public_html/artisan schedule:run`

### Using VPS (DigitalOcean, Linode, AWS)

```bash
# Install dependencies
sudo apt update
sudo apt install php8.2-cli php8.2-fpm nginx supervisor

# Configure Nginx + PHP-FPM
# Set up SSL with Let's Encrypt
# Configure Supervisor for queue worker
# Add cron job for scheduler
```

### Using Platform-as-a-Service (Laravel Forge, Vapor)

Laravel Forge automatically configures:
- Cron scheduler
- Queue workers
- Deployment scripts
- SSL certificates

Just deploy your code and it works!

## Notification Logic Summary

The system checks projects twice daily:

**Morning Check (9:00 AM):**
- Sends notifications for ALL cases (3, 2, 1 days, due today, overdue)

**Evening Check (6:00 PM):**
- Only sends for 3-day reminders (evening reminder)

**Urgency Levels:**
- **3 days**: Normal reminder with morning/evening variants
- **2 days**: Moderate alert (⚠️)
- **1 day**: HIGH alert (🚨)
- **Due today**: CRITICAL alert (🔴)
- **Overdue**: Overdue alert (❌)

## API for Custom Integrations

You can trigger notifications programmatically:

```php
use App\Services\NotificationService;
use App\Models\Project;
use App\Models\User;

$notificationService = app(NotificationService::class);

// Send reminder for specific project
$project = Project::find($projectId);
$user = User::find($userId);
$notificationService->sendProjectDueReminder($project, $user, $daysRemaining, 'morning');
```

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review queue status: `php artisan queue:failed`
3. Test email config: `php artisan tinker` then send test email
4. Verify scheduler: `php artisan schedule:list`

---

**Last Updated**: 2025-10-28
**Version**: 2.0 (Enhanced Notification System)
