# Push Notifications Setup Guide - MyTime

This guide explains how to set up and use the push notification system for real-time 24/7 notifications for project dues and new projects.

## Overview

The push notification system includes:
- **Browser Push Notifications**: Real-time notifications on Windows/Mac/Linux
- **Mobile Push Notifications**: Support for mobile devices
- **Service Worker**: Background service for handling notifications
- **Scheduled Checks**: Hourly and daily checks for project due dates
- **Email Fallback**: Email notifications as backup

## Installation Steps

### 1. Run Database Migration

```bash
php artisan migrate
```

This creates the following columns in the `users` table:
- `push_notifications` (boolean) - Enable/disable push notifications
- `push_subscription` (text) - Stores the push subscription details
- `last_push_notification_at` (timestamp) - Tracks last notification sent

### 2. Update User Model

The User model has been updated with:
```php
protected $fillable = [
    // ... existing fields
    'push_notifications',
    'push_subscription',
    'last_push_notification_at',
];

protected function casts(): array
{
    return [
        // ... existing casts
        'push_notifications' => 'boolean',
    ];
}
```

### 3. Service Files Created

The following service files have been created:

#### `app/Services/PushNotificationService.php`
- Handles sending push notifications to users
- Methods:
  - `sendPushNotification()` - Send notification to a user
  - `sendBulkPushNotifications()` - Send to multiple users
  - `sendProjectDueNotification()` - Project due notifications
  - `sendNewProjectNotification()` - New project notifications
  - `sendProjectCompletionNotification()` - Project completion notifications
  - `sendTimeTrackingReminder()` - Time tracking reminders
  - `sendTestNotification()` - Test notifications

#### `app/Services/NotificationService.php` (Updated)
- Now includes push notification integration
- Automatically sends push notifications when:
  - Project due dates are approaching
  - New projects are created
  - Projects are completed

### 4. Controller Created

#### `app/Http/Controllers/PushNotificationController.php`
Routes:
- `POST /push-notifications/subscribe` - Subscribe to push notifications
- `POST /push-notifications/unsubscribe` - Unsubscribe from push notifications
- `POST /push-notifications/toggle` - Toggle push notifications on/off
- `POST /push-notifications/test` - Send test notification
- `GET /push-notifications/status` - Get notification status

### 5. Frontend Files

#### `public/service-worker.js`
- Handles push notifications in the browser
- Manages notification display
- Handles notification clicks and actions
- Supports offline functionality

#### `public/js/push-notifications.js`
- Registers service worker
- Manages push subscriptions
- Handles notification permissions
- Provides JavaScript API for push notifications

### 6. Layout Updated

The main layout (`resources/views/layouts/app.blade.php`) now includes:
- Push notification script loading
- Service worker registration
- Automatic subscription handling

## How It Works

### Real-Time Notifications

1. **User Subscribes**
   - When user visits the app, the service worker is registered
   - Browser requests notification permission
   - User subscription is sent to the server

2. **Scheduled Checks**
   - Every hour: `projects:check-due-dates` command runs
   - Checks for projects due in 3, 2, 1, 0, or overdue days
   - Sends push notifications to all affected users

3. **Notification Delivery**
   - Push notification is sent to user's browser
   - Browser displays notification even if app is closed
   - User can click notification to open project

### Project Due Notifications

Notifications are sent based on days remaining:

- **3 days before**: 2 reminders per day (9 AM & 6 PM)
- **2 days before**: 1 moderate alert (9 AM)
- **1 day before**: 1 high alert (9 AM)
- **Due today**: Critical alert (9 AM)
- **Overdue**: Daily alert (9 AM)

### New Project Notifications

When a new project is created:
- Project creator receives notification
- All team members receive notification
- Notification includes project name and link

## Configuration

### Enable/Disable Push Notifications

Users can manage push notifications in their profile:

```javascript
// Toggle push notifications
await window.pushNotificationManager.togglePushNotifications(true);

// Get status
const status = await window.pushNotificationManager.getPushNotificationStatus();

// Send test notification
await window.pushNotificationManager.sendTestNotification();
```

### Scheduled Tasks

The scheduler is configured in `routes/console.php`:

```php
// Hourly check
Schedule::command('projects:check-due-dates')
    ->hourly()
    ->name('realtime-project-check')
    ->runInBackground();

// Morning check at 9 AM
Schedule::command('projects:check-due-dates')
    ->dailyAt('09:00')
    ->name('morning-comprehensive-check');

// Evening check at 6 PM
Schedule::command('projects:check-due-dates')
    ->dailyAt('18:00')
    ->name('evening-reminder-check');
```

## Testing

### Test Push Notifications

1. **Via API**
   ```bash
   POST /push-notifications/test
   ```

2. **Via JavaScript**
   ```javascript
   window.pushNotificationManager.sendTestNotification();
   ```

3. **Via Artisan Command**
   ```bash
   php artisan tinker
   $user = User::first();
   app(PushNotificationService::class)->sendTestNotification($user);
   ```

### Test Project Due Notifications

1. Create a project with end date 3 days from now
2. Run: `php artisan projects:check-due-dates`
3. Check browser for notification

## Browser Support

Push notifications are supported in:
- ✅ Chrome/Chromium (Windows, Mac, Linux)
- ✅ Firefox (Windows, Mac, Linux)
- ✅ Edge (Windows)
- ✅ Opera (Windows, Mac, Linux)
- ✅ Android Chrome
- ✅ Firefox Android
- ❌ Safari (limited support)
- ❌ Internet Explorer

## Troubleshooting

### Notifications Not Appearing

1. **Check browser permissions**
   - Go to browser settings
   - Ensure notifications are allowed for the domain

2. **Check service worker**
   - Open DevTools (F12)
   - Go to Application > Service Workers
   - Ensure service worker is registered and active

3. **Check subscription**
   ```javascript
   const status = await window.pushNotificationManager.getPushNotificationStatus();
   console.log(status);
   ```

4. **Check logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Service Worker Not Registering

1. Ensure HTTPS is enabled (required for service workers)
2. Check browser console for errors
3. Clear browser cache and reload

### Notifications Sent But Not Displayed

1. Check if notifications are muted in browser settings
2. Check if "Do Not Disturb" is enabled on OS
3. Verify notification permission is granted

## Database Queries

### Check User Subscriptions

```php
// Users with push notifications enabled
$users = User::where('push_notifications', true)
    ->whereNotNull('push_subscription')
    ->get();

// Users without subscriptions
$users = User::where('push_notifications', true)
    ->whereNull('push_subscription')
    ->get();
```

### Check Notification History

```php
// Last notifications sent
$notifications = Notification::latest()
    ->limit(10)
    ->get();

// Notifications for specific user
$notifications = Auth::user()->notifications()->latest()->get();
```

## Performance Considerations

1. **Batch Processing**: Notifications are sent in batches to avoid server overload
2. **Caching**: Subscription data is cached to reduce database queries
3. **Background Jobs**: Consider using queues for large-scale notifications
4. **Rate Limiting**: Implement rate limiting to prevent notification spam

## Security

1. **CSRF Protection**: All endpoints are protected with CSRF tokens
2. **Authentication**: All routes require user authentication
3. **Validation**: All input is validated before processing
4. **Encryption**: Subscription data is stored securely

## Future Enhancements

1. **Web Push Protocol**: Implement full Web Push Protocol for better reliability
2. **Mobile Apps**: Native mobile app support with Firebase Cloud Messaging
3. **Notification Preferences**: Allow users to customize notification types
4. **Notification History**: Store and display notification history
5. **Analytics**: Track notification delivery and engagement rates

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review browser console for errors
3. Check Laravel logs in `storage/logs/laravel.log`
4. Contact support with error details

## References

- [Web Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API)
- [Service Workers](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Notifications API](https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API)
- [Laravel Scheduling](https://laravel.com/docs/scheduling)
