# Push Notifications - Quick Start Guide

## 5-Minute Setup

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Verify Files Are in Place
- ✅ `app/Services/PushNotificationService.php`
- ✅ `app/Http/Controllers/PushNotificationController.php`
- ✅ `public/service-worker.js`
- ✅ `public/js/push-notifications.js`
- ✅ `routes/web.php` (updated with push routes)
- ✅ `routes/console.php` (scheduler configured)

### Step 3: Start the Scheduler
```bash
# For development (runs in foreground)
php artisan schedule:work

# For production (add to cron)
* * * * * cd /path/to/mytime && php artisan schedule:run >> /dev/null 2>&1
```

### Step 4: Test It Out

1. **Open the app in browser**
   - Navigate to http://localhost:8000/dashboard
   - Browser will request notification permission
   - Click "Allow"

2. **Send test notification**
   - Open browser console (F12)
   - Run: `window.pushNotificationManager.sendTestNotification()`
   - You should see a notification appear

3. **Create a test project**
   - Create a new project with end date 3 days from now
   - Run: `php artisan projects:check-due-dates`
   - You should receive a push notification

## What Happens Automatically

### Every Hour
- System checks all projects for due dates
- Sends notifications to affected users
- Logs all activity

### Every Day at 9 AM
- Morning comprehensive check
- Sends all pending notifications

### Every Day at 6 PM
- Evening reminder check
- Sends evening notifications

## User Experience

### First Time User
1. Opens app → Browser asks for permission
2. User clicks "Allow"
3. Service worker registers
4. User is subscribed to push notifications

### Receiving Notifications
1. User gets notification even if app is closed
2. Notification shows project name and urgency
3. Clicking notification opens the project
4. Notification is marked as read

### Managing Notifications
Users can:
- Enable/disable in profile settings
- Send test notification
- View notification history
- Check notification status

## Notification Types

### Project Due Notifications
- **3 days before**: "📅 Project Due in 3 Days"
- **2 days before**: "⚠️ Moderate Alert: Project Due in 2 Days"
- **1 day before**: "🚨 HIGH ALERT: Project Due Tomorrow!"
- **Due today**: "🔴 CRITICAL: Project Due TODAY!"
- **Overdue**: "❌ OVERDUE: Project Deadline Passed!"

### New Project Notifications
- "✨ New Project Added!"
- Shows project name
- Links to project details

### Project Completion Notifications
- "✅ Project Completed!"
- Shows project name

## Troubleshooting

### Notifications Not Working?

1. **Check browser permissions**
   ```
   Settings → Privacy → Notifications → Allow for this site
   ```

2. **Check service worker**
   - Open DevTools (F12)
   - Application → Service Workers
   - Should show "service-worker.js" as "activated and running"

3. **Check scheduler**
   ```bash
   php artisan schedule:list
   ```
   Should show 3 scheduled commands

4. **Check logs**
   ```bash
   tail -f storage/logs/laravel.log | grep -i notification
   ```

### Service Worker Not Registering?

1. Ensure HTTPS is enabled (or localhost)
2. Clear browser cache
3. Check browser console for errors
4. Restart the development server

### Notifications Sent But Not Displayed?

1. Check if notifications are muted in OS settings
2. Check if "Do Not Disturb" is enabled
3. Check browser notification settings
4. Try a different browser

## API Endpoints

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

## JavaScript API

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

## Database Schema

### users table additions
```sql
ALTER TABLE users ADD COLUMN push_notifications BOOLEAN DEFAULT TRUE;
ALTER TABLE users ADD COLUMN push_subscription LONGTEXT NULL;
ALTER TABLE users ADD COLUMN last_push_notification_at TIMESTAMP NULL;
```

## Cron Job Setup (Production)

Add to your server's crontab:

```bash
# Run Laravel scheduler every minute
* * * * * cd /path/to/mytime && php artisan schedule:run >> /dev/null 2>&1
```

Or use supervisor for the schedule:work command:

```ini
[program:mytime-scheduler]
process_name=%(program_name)s
command=php /path/to/mytime/artisan schedule:work
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/mytime-scheduler.log
```

## Performance Tips

1. **Use background jobs for bulk notifications**
   ```php
   Queue::dispatch(new SendBulkNotifications($users, $title, $message));
   ```

2. **Implement rate limiting**
   ```php
   if (RateLimiter::tooManyAttempts('push-notification:' . $user->id, 5)) {
       return; // Skip this notification
   }
   ```

3. **Cache subscription data**
   ```php
   $subscriptions = Cache::remember('user-subscriptions', 3600, function () {
       return User::where('push_notifications', true)
           ->whereNotNull('push_subscription')
           ->get();
   });
   ```

## Next Steps

1. ✅ Run migration
2. ✅ Start scheduler
3. ✅ Test notifications
4. ✅ Configure cron job (production)
5. ✅ Monitor logs
6. ✅ Gather user feedback

## Support

For detailed information, see:
- `PUSH_NOTIFICATIONS_SETUP.md` - Complete setup guide
- `storage/logs/laravel.log` - Application logs
- Browser DevTools - Service worker and console logs

## Success Indicators

✅ Service worker registered and active
✅ Test notification received
✅ Project due notifications working
✅ New project notifications working
✅ Scheduler running every hour
✅ Users receiving notifications 24/7

You're all set! 🎉
