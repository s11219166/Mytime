# 🚀 Quick Start: Enable Notifications for 24/7 Hosting

## ⚡ Fast Setup (5 Minutes)

### 1. Configure Email (`.env` file)
```env
# Use your email service
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mytime.com
MAIL_FROM_NAME="MyTime"
```

### 2. Setup Queue Database
```bash
php artisan queue:table
php artisan migrate
```

### 3. Add Cron Job (Linux/Mac) - REQUIRED for 24/7
```bash
crontab -e
# Add this line:
* * * * * cd /path/to/mytime && php artisan schedule:run >> /dev/null 2>&1
```

**Windows Server Task Scheduler:**
- Trigger: Every 1 minute
- Action: `php.exe artisan schedule:run`
- Start in: Your project path

### 4. Start Queue Worker
```bash
# For testing (temporary)
php artisan queue:work --tries=3

# For production (using Supervisor - recommended)
# See NOTIFICATION_SYSTEM_SETUP.md for full instructions
```

### 5. Test It
```bash
# Test the notification command
php artisan projects:check-due-dates

# View scheduled tasks
php artisan schedule:list
```

## 📅 Notification Schedule

| Days Before Due | Times Per Day | When        | Alert Level |
|-----------------|---------------|-------------|-------------|
| 3 days          | 2x            | 9AM & 6PM   | 📅 Normal   |
| 2 days          | 1x            | 9AM         | ⚠️ Moderate |
| 1 day           | 1x            | 9AM         | 🚨 HIGH     |
| Due today       | 1x            | 9AM         | 🔴 CRITICAL |
| Overdue         | 1x daily      | 9AM         | ❌ Overdue  |

## ✅ Checklist for Production

- [ ] Email configured in `.env`
- [ ] Queue table created
- [ ] Cron job added (runs every minute)
- [ ] Queue worker running (Supervisor/systemd)
- [ ] Test email sent successfully
- [ ] User notifications enabled in database

## 🔍 Common Commands

```bash
# Check scheduled tasks
php artisan schedule:list

# Run notifications manually (for testing)
php artisan projects:check-due-dates

# View queue status
php artisan queue:monitor database

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Test email
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
```

## 🐛 Troubleshooting

**No emails sent?**
1. Check queue worker is running: `ps aux | grep queue:work`
2. Check failed jobs: `php artisan queue:failed`
3. Verify email in `.env`

**Notifications not triggering?**
1. Verify cron is running: `crontab -l`
2. Check logs: `tail -f storage/logs/laravel.log`
3. Test manually: `php artisan projects:check-due-dates`

**Current time check:**
```bash
# See current hour and if it's in notification window
php -r "echo 'Hour: ' . date('H') . ' (Morning: 8-12, Evening: 17-21)';"
```

## 📚 Full Documentation

See `NOTIFICATION_SYSTEM_SETUP.md` for complete setup guide including:
- Production server configurations
- Supervisor/systemd setup
- Different hosting platforms
- Monitoring and maintenance

---
**Ready to go live!** 🎉
