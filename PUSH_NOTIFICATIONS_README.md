# 🔔 MyTime Push Notifications System

A comprehensive real-time push notification system for the MyTime project management application. Sends Windows/mobile notifications 24/7 for project due dates and new projects.

## ✨ Features

- 🔔 **Real-Time Notifications** - Instant delivery to browser and mobile devices
- 📅 **Smart Scheduling** - Automatic reminders 3, 2, 1 days before, on due date, and when overdue
- 🆕 **New Project Alerts** - Instant notifications when projects are created or assigned
- ✅ **Project Completion** - Notifications when projects are marked complete
- ⏱️ **Time Tracking Reminders** - Optional reminders to log time
- 🌐 **Cross-Browser Support** - Works on Chrome, Firefox, Edge, Opera, and mobile browsers
- 📱 **Mobile Ready** - Full support for Android and other mobile devices
- 🔐 **Secure** - CSRF protected, authenticated, encrypted subscriptions
- 🚀 **Scalable** - Batch processing, queue-ready architecture
- 📊 **24/7 Monitoring** - Automatic hourly checks with morning and evening reminders

## 🚀 Quick Start

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Start Scheduler
```bash
php artisan schedule:work
```

### 3. Open App
```
http://localhost:8000/dashboard
```

### 4. Allow Notifications
- Browser will ask for permission
- Click "Allow"

### 5. Test It
```javascript
// Open browser console (F12) and run:
window.pushNotificationManager.sendTestNotification()
```

That's it! You're ready to receive notifications. 🎉

## 📋 What's Included

### Backend Components
- **PushNotificationService** - Core service for sending notifications
- **PushNotificationController** - API endpoints for managing subscriptions
- **Database Migration** - Adds push notification fields to users table
- **Scheduler** - Automatic hourly, morning, and evening checks

### Frontend Components
- **Service Worker** - Handles push notifications in browser
- **Push Notification Manager** - JavaScript API for managing subscriptions
- **Layout Integration** - Automatic service worker registration

### Documentation
- **PUSH_NOTIFICATIONS_QUICK_START.md** - Get started in 5 minutes
- **PUSH_NOTIFICATIONS_SETUP.md** - Complete setup guide
- **PUSH_NOTIFICATIONS_IMPLEMENTATION.md** - Implementation checklist
- **PUSH_NOTIFICATIONS_DEPLOYMENT.md** - Production deployment guide
- **PUSH_NOTIFICATIONS_SUMMARY.md** - Complete feature summary

## 📊 Notification Schedule

### Project Due Notifications
| Days Remaining | Frequency | Time |
|---|---|---|
| 3 days | 2x daily | 9 AM & 6 PM |
| 2 days | 1x daily | 9 AM |
| 1 day | 1x daily | 9 AM |
| 0 days (Today) | 1x daily | 9 AM |
| Overdue | 1x daily | 9 AM |

### Scheduler Runs
- **Every Hour** - Real-time project checks
- **Daily 9 AM** - Morning comprehensive check
- **Daily 6 PM** - Evening reminder check

## 🔌 API Endpoints

### Subscribe to Notifications
```bash
POST /push-notifications/subscribe
Content-Type: application/json

{
  "endpoint": "https://...",
  "keys": {
    "p256dh": "...",
    "auth": "..."
  }
}
```

### Unsubscribe
```bash
POST /push-notifications/unsubscribe
```

### Toggle Notifications
```bash
POST /push-notifications/toggle
Content-Type: application/json

{
  "enabled": true
}
```

### Send Test Notification
```bash
POST /push-notifications/test
```

### Get Status
```bash
GET /push-notifications/status
```

## 💻 JavaScript API

```javascript
// Get the manager
const manager = window.pushNotificationManager;

// Check if supported
if (manager.isSupported) {
  console.log('Push notifications supported');
}

// Send test notification
await manager.sendTestNotification();

// Toggle notifications
await manager.togglePushNotifications(true);

// Get status
const status = await manager.getPushNotificationStatus();
console.log(status);
// Output: { enabled: true, subscribed: true, lastNotification: "2024-01-15..." }

// Unsubscribe
await manager.unsubscribeToPushNotifications();
```

## 🌐 Browser Support

| Browser | Windows | Mac | Linux | Android | iOS |
|---|---|---|---|---|---|
| Chrome | ✅ | ✅ | ✅ | ✅ | ❌ |
| Firefox | ✅ | ✅ | ✅ | ✅ | ❌ |
| Edge | ✅ | ✅ | ✅ | ✅ | ❌ |
| Opera | ✅ | ✅ | ✅ | ✅ | ❌ |
| Safari | ❌ | ❌ | ❌ | ❌ | ❌ |

## 📁 File Structure

```
mytime/
├── app/
│   ├── Services/
│   │   ├── PushNotificationService.php (NEW)
│   │   └── NotificationService.php (UPDATED)
│   ├── Http/Controllers/
│   │   └── PushNotificationController.php (NEW)
│   └── Models/
│       ├── User.php (UPDATED)
│       └��─ Project.php (UPDATED)
├── public/
│   ├── service-worker.js (NEW)
│   └── js/
│       └── push-notifications.js (NEW)
├── database/
│   └── migrations/
│       └── 2025_01_15_000000_add_push_notification_fields_to_users_table.php (NEW)
├── routes/
│   ├── web.php (UPDATED)
│   └── console.php (ALREADY CONFIGURED)
├── resources/
│   └── views/
│       └── layouts/
│           └── app.blade.php (UPDATED)
└── Documentation/
    ├── PUSH_NOTIFICATIONS_README.md (THIS FILE)
    ├── PUSH_NOTIFICATIONS_QUICK_START.md
    ├── PUSH_NOTIFICATIONS_SETUP.md
    ├── PUSH_NOTIFICATIONS_IMPLEMENTATION.md
    ├── PUSH_NOTIFICATIONS_DEPLOYMENT.md
    └── PUSH_NOTIFICATIONS_SUMMARY.md
```

## 🔧 Configuration

### Adjust Notification Timing
Edit `routes/console.php`:
```php
// Change morning time from 9 AM to 8 AM
Schedule::command('projects:check-due-dates')
    ->dailyAt('08:00')
    ->name('morning-comprehensive-check');

// Change to every 30 minutes instead of hourly
Schedule::command('projects:check-due-dates')
    ->everyThirtyMinutes()
    ->name('realtime-project-check');
```

### Disable for Specific User
```php
$user->update(['push_notifications' => false]);
```

### Check Active Subscriptions
```php
php artisan tinker
>>> User::where('push_notifications', true)->whereNotNull('push_subscription')->count()
```

## 🧪 Testing

### Test Notification
```javascript
window.pushNotificationManager.sendTestNotification()
```

### Check Status
```javascript
const status = await window.pushNotificationManager.getPushNotificationStatus()
console.log(status)
```

### Manual Scheduler Test
```bash
php artisan projects:check-due-dates
```

### Create Test Project
```php
php artisan tinker
>>> $project = Project::create([
...   'name' => 'Test Project',
...   'end_date' => now()->addDays(3),
...   'created_by' => 1
... ]);
>>> exit
```

## 🐛 Troubleshooting

### Notifications Not Appearing?
1. Check browser notification settings
2. Verify service worker is active (DevTools → Application)
3. Check browser console for errors
4. Ensure notification permission is granted

### Service Worker Not Registering?
1. Ensure HTTPS is enabled (or using localhost)
2. Clear browser cache
3. Check browser console for errors
4. Restart development server

### Scheduler Not Running?
1. Verify cron job is set up
2. Run: `php artisan schedule:work`
3. Check Laravel logs
4. Verify database connection

### Subscription Not Saving?
1. Verify database migration ran
2. Check user table has new columns
3. Verify CSRF token is present
4. Check browser console for errors

## 📊 Database Schema

### New Columns in `users` Table
```sql
push_notifications BOOLEAN DEFAULT TRUE
push_subscription LONGTEXT NULL
last_push_notification_at TIMESTAMP NULL
```

## 🔐 Security

- ✅ CSRF protection on all endpoints
- ✅ Authentication required for all routes
- ✅ Input validation on all endpoints
- ✅ Secure subscription storage
- ✅ Rate limiting support
- ✅ Error handling and logging

## 📈 Performance

- ✅ Batch processing for bulk notifications
- ✅ Efficient database queries
- ✅ Caching support
- ✅ Background job ready
- ✅ Scalable architecture

## 🚀 Production Deployment

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Set Up Cron Job
```bash
# Add to crontab
* * * * * cd /path/to/mytime && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Verify Installation
```bash
php artisan schedule:list
```

### 4. Monitor
```bash
tail -f storage/logs/laravel.log | grep -i notification
```

For detailed deployment instructions, see **PUSH_NOTIFICATIONS_DEPLOYMENT.md**

## 📚 Documentation

- **PUSH_NOTIFICATIONS_QUICK_START.md** - Get started in 5 minutes
- **PUSH_NOTIFICATIONS_SETUP.md** - Complete setup guide with all details
- **PUSH_NOTIFICATIONS_IMPLEMENTATION.md** - Implementation checklist
- **PUSH_NOTIFICATIONS_DEPLOYMENT.md** - Production deployment guide
- **PUSH_NOTIFICATIONS_SUMMARY.md** - Complete feature summary

## 🎯 Key Features

✅ Real-Time Notifications
✅ 24/7 Monitoring
✅ Multiple Notification Types
✅ Cross-Browser Support
✅ Mobile Ready
✅ Offline Support
✅ User Control
✅ Secure
✅ Scalable
✅ Well Documented

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review logs in `storage/logs/laravel.log`
3. Check browser console (F12)
4. Review the documentation files

## 🎉 Success Indicators

After setup, verify:
- ✅ Service worker registered and active
- ✅ Test notification received
- ✅ Project due notifications working
- ✅ New project notifications working
- ✅ Scheduler running every hour
- ✅ Users receiving notifications 24/7
- ✅ No errors in logs

## 📝 Changelog

### Version 1.0 (January 15, 2025)
- Initial release
- Push notification system implemented
- Service worker integration
- Scheduler configuration
- Complete documentation

## 📄 License

This push notification system is part of the MyTime application.

## 👥 Contributors

- Development Team
- QA Team
- Documentation Team

## 🙏 Acknowledgments

Built with:
- Laravel
- Service Workers
- Web Push API
- Notifications API

---

**Status:** ✅ Production Ready

**Last Updated:** January 15, 2025

**Version:** 1.0

**Ready to use!** 🚀
