# Quick Start Guide - Notification System

## ✅ System Status: WORKING

The notification system is now fully functional and sending both in-app notifications and email alerts for project due dates.

## What Was Fixed

**Problem**: The system was calculating days remaining as decimal numbers (e.g., 0.707 days) but checking for exact integers (0, 1, 2, etc.)

**Solution**: Added `floor()` function to convert decimal days to integers before comparison.

## How It Works Now

### Automatic Notifications Based on Priority

| Priority | Notification Days Before Due Date |
|----------|-----------------------------------|
| 🚨 **Urgent** | 5, 3, 2, 1, 0 days + overdue |
| 🔴 **High** | 7, 3, 1, 0 days + overdue |
| 🟡 **Medium** | 7, 3, 0 days + overdue |
| 🟢 **Low** | 7, 0 days + overdue |

### What Gets Sent

1. **In-App Notification** - Appears in the notification bell icon
2. **Email Notification** - Sent to user's email (if enabled)

## Quick Test

Run this command to send notifications now:
```bash
php artisan projects:check-due-dates
```

You should see:
```
Checking project due dates...
Project due date check completed!
```

## Verify It's Working

### Check In-App Notifications
1. Log into the application
2. Click the bell icon in the top navigation
3. You should see notifications for projects due soon or overdue

### Check Email
1. Look in your inbox for emails from "MyTime"
2. Subject line: "Project Due Reminder: [Project Name]"
3. Check spam folder if not in inbox

### Check Database
```bash
php artisan tinker --execute="echo App\Models\Notification::count();"
```

## Set Up Automated Daily Notifications

### Option 1: Laravel Scheduler (Recommended)

1. Add to your server's crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

2. Create/edit `routes/console.php` and add:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('projects:check-due-dates')->dailyAt('09:00');
```

### Option 2: Direct Cron Job

Add to crontab (runs daily at 9 AM):
```bash
0 9 * * * cd /path-to-your-project && php artisan projects:check-due-dates
```

## User Settings

Users can control their notifications in their profile:

- **Email Notifications**: ON/OFF toggle for all email notifications
- **Project Updates**: ON/OFF toggle for project-related notifications

## Email Features

The email includes:
- ✅ Priority-colored header (red for urgent, orange for high, etc.)
- ✅ Priority badge
- ✅ Days remaining or overdue status
- ✅ Project details (status, dates, progress)
- ✅ Progress bar visualization
- ✅ Direct link to view project
- ✅ Special urgent alerts for high-priority projects

## Troubleshooting

### No notifications appearing?
1. Check if projects have due dates set
2. Verify project status is not "completed" or "cancelled"
3. Run the command manually: `php artisan projects:check-due-dates`

### No emails being sent?
1. Check user profile has "Email Notifications" enabled
2. Verify `.env` mail settings are correct
3. Check spam/junk folder
4. Check logs: `storage/logs/laravel.log`

### Test email sending:
```bash
php artisan tinker
Mail::raw('Test', function($msg) { $msg->to('your@email.com')->subject('Test'); });
```

## Current Configuration

**Mail Settings** (from .env):
- Provider: Gmail SMTP
- From: chandsalvesh7@gmail.com
- Name: MyTime

**Notification Triggers**:
- Urgent projects: Start at 5 days before due date
- All priorities: Continue notifications when overdue
- Frequency: Once per day (when scheduled)

## Files Modified

1. `app/Services/NotificationService.php` - Fixed days calculation
2. `resources/views/emails/project-due-reminder.blade.php` - Enhanced email template
3. `app/Console/Commands/CheckProjectDueDates.php` - Command handler

## Support

For detailed information, see:
- `NOTIFICATION_FIX_SUMMARY.md` - Complete technical details
- `NOTIFICATION_UPDATE_README.md` - Feature documentation
- `EMAIL_TESTING_GUIDE.md` - Email testing instructions

## Success! 🎉

Your notification system is now:
- ✅ Sending in-app notifications
- ✅ Sending email notifications
- ✅ Prioritizing urgent projects (5-day advance notice)
- ✅ Covering all priority levels
- ✅ Continuing notifications for overdue projects

Just set up the automated scheduling and you're all set!
