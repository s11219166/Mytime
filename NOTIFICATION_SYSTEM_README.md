# Notification System - MyTime Application

## Overview

A comprehensive notification system has been implemented for the MyTime project management application. This system provides in-app notifications and email reminders for project due dates, deadlines, and other important events.

## Features Implemented

### 1. **In-App Notifications**
- Real-time notification display in the sidebar
- Unread notification count badge
- Dedicated notifications page with filtering
- Mark as read/unread functionality
- Delete notifications
- Clear all read notifications

### 2. **Email Notifications**
- Beautiful HTML email templates
- Project due date reminders
- Overdue project alerts
- Customizable email preferences per user

### 3. **Notification Types**
- `project_due` - Project due today
- `project_overdue` - Project is overdue
- `project_reminder` - Upcoming project deadline (7, 3, 1 days before)
- `project_assigned` - User assigned to new project
- `project_completed` - Project marked as completed
- `time_reminder` - Time tracking reminders

### 4. **Automated Reminders**
- Automatic checks for upcoming due dates
- Reminders sent at: 7 days, 3 days, 1 day, and on due date
- Overdue notifications for past deadlines
- Respects user notification preferences

## Database Structure

### Notifications Table
```sql
- id (primary key)
- user_id (foreign key to users)
- project_id (foreign key to projects, nullable)
- type (notification type)
- title (notification title)
- message (notification message)
- data (JSON - additional data)
- is_read (boolean)
- read_at (timestamp)
- created_at
- updated_at
```

## Files Created/Modified

### New Files:
1. **Migration**: `database/migrations/2025_10_02_010921_create_notifications_table.php`
2. **Model**: `app/Models/Notification.php`
3. **Controller**: `app/Http/Controllers/NotificationController.php`
4. **Service**: `app/Services/NotificationService.php`
5. **Mail**: `app/Mail/ProjectDueReminderMail.php`
6. **Email Template**: `resources/views/emails/project-due-reminder.blade.php`
7. **Command**: `app/Console/Commands/CheckProjectDueDates.php`

### Modified Files:
1. `app/Models/User.php` - Added notifications relationship
2. `routes/web.php` - Added notification routes
3. `resources/views/notifications.blade.php` - Updated with dynamic content
4. `resources/views/layouts/app.blade.php` - Added real-time notification count

## API Endpoints

### Notification Routes:
- `GET /notifications` - View all notifications
- `GET /notifications/unread-count` - Get unread count (AJAX)
- `GET /notifications/recent` - Get recent notifications (AJAX)
- `POST /notifications/{id}/read` - Mark notification as read
- `POST /notifications/mark-all-read` - Mark all as read
- `DELETE /notifications/{id}` - Delete notification
- `POST /notifications/clear-read` - Clear all read notifications

## Usage

### Creating Notifications Programmatically

```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);

// Send project due reminder
$notificationService->sendProjectDueReminder($project, $user, $daysRemaining);

// Notify project assignment
$notificationService->notifyProjectAssignment($project, $user);

// Notify project completion
$notificationService->notifyProjectCompletion($project);

// Send time tracking reminder
$notificationService->sendTimeTrackingReminder($user);
```

### Running the Due Date Checker

#### Manual Execution:
```bash
php artisan projects:check-due-dates
```

#### Scheduled Execution (Add to `app/Console/Kernel.php`):
```php
protected function schedule(Schedule $schedule)
{
    // Check project due dates daily at 9 AM
    $schedule->command('projects:check-due-dates')
             ->dailyAt('09:00');
    
    // Or check multiple times per day
    $schedule->command('projects:check-due-dates')
             ->twiceDaily(9, 17); // 9 AM and 5 PM
}
```

## Email Configuration

### Setup Email in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # or your SMTP server
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mytime.com
MAIL_FROM_NAME="MyTime"
```

### For Testing (Mailtrap):
1. Sign up at https://mailtrap.io
2. Get your SMTP credentials
3. Update `.env` with credentials
4. All emails will be caught in Mailtrap inbox

### For Production (Gmail):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="MyTime"
```

## User Notification Preferences

Users can control their notification settings in their profile:

- **Email Notifications**: Enable/disable all email notifications
- **Project Updates**: Receive notifications about project changes
- **Time Reminders**: Get reminders to log time
- **Weekly Reports**: Receive weekly summary emails

## Notification Flow

### Project Due Date Reminders:

1. **Command runs** (manually or via scheduler)
2. **System checks** all active projects with end dates
3. **For each project** that's due in 7, 3, 1 days or overdue:
   - Creates in-app notification for project creator
   - Creates in-app notification for team members (if they have project_updates enabled)
   - Sends email to users (if they have email_notifications enabled)
4. **Users receive**:
   - In-app notification (visible immediately)
   - Email notification (if enabled)
   - Notification badge count updates

## Email Template Features

The email template includes:
- Responsive design
- Light green branding
- Project details (name, status, priority, dates, progress)
- Visual progress bar
- Color-coded alerts (warning for upcoming, danger for overdue)
- Direct link to project
- Professional footer

## Testing the System

### 1. Create Test Notifications:
```php
// In tinker or a test route
use App\Services\NotificationService;
use App\Models\Project;
use App\Models\User;

$service = app(NotificationService::class);
$project = Project::first();
$user = User::first();

// Test due reminder
$service->sendProjectDueReminder($project, $user, 3);
```

### 2. Test Email Sending:
```bash
php artisan tinker

use App\Mail\ProjectDueReminderMail;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

$project = Project::first();
$user = User::first();

Mail::to($user->email)->send(new ProjectDueReminderMail($project, $user, 3));
```

### 3. Test Command:
```bash
php artisan projects:check-due-dates
```

## Customization

### Adding New Notification Types:

1. **Add type to Notification model** icon and color methods
2. **Create notification method** in NotificationService
3. **Call the method** where needed in your application

Example:
```php
// In NotificationService.php
public function notifyTaskAssignment(Task $task, User $user): void
{
    $this->createNotification(
        $user,
        'task_assigned',
        'New Task Assignment',
        "You have been assigned to task '{$task->name}'.",
        $task->project,
        ['task_id' => $task->id]
    );
}
```

## Troubleshooting

### Emails Not Sending:
1. Check `.env` mail configuration
2. Verify SMTP credentials
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with Mailtrap first

### Notifications Not Appearing:
1. Check database: `SELECT * FROM notifications`
2. Verify user has notifications relationship
3. Check browser console for JavaScript errors
4. Clear cache: `php artisan cache:clear`

### Scheduler Not Running:
1. Add to cron (Linux/Mac):
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```
2. Or use Windows Task Scheduler
3. Or run manually for testing

## Performance Considerations

- Notifications are paginated (20 per page)
- Old notifications can be archived/deleted
- Email sending can be queued for better performance
- Consider adding indexes on `user_id` and `is_read` columns

## Future Enhancements

- [ ] Push notifications (browser/mobile)
- [ ] SMS notifications
- [ ] Notification preferences per notification type
- [ ] Notification grouping
- [ ] Real-time notifications with WebSockets
- [ ] Notification templates management
- [ ] Notification scheduling
- [ ] Digest emails (daily/weekly summaries)

## Security

- All notification routes are protected by authentication middleware
- Users can only see their own notifications
- CSRF protection on all POST/DELETE requests
- XSS protection via Blade templating
- SQL injection protection via Eloquent ORM

## Color Scheme

Notifications use the light green theme:
- Unread: Light green background (#f0fff4)
- Border: Lime green (#32CD32)
- Icons: Color-coded by type
- Email: Light green gradient header

## Support

For issues or questions about the notification system:
1. Check Laravel logs
2. Review this documentation
3. Test with simple examples first
4. Verify database migrations ran successfully
