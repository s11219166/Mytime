# MyTime Notification System - Complete Documentation

## System Overview

The notification system consists of:

1. **Database Model** (`app/Models/Notification.php`)
   - Stores all notifications with user, project, type, title, message, and metadata
   - Supports marking as read and provides icon/color attributes based on type

2. **Notification Service** (`app/Services/NotificationService.php`)
   - Creates notifications in the database
   - Sends project due date reminders with urgency levels
   - Handles project assignments and completions
   - Sends time tracking reminders
   - Checks project due dates and sends appropriate notifications

3. **Mail Class** (`app/Mail/ProjectDueReminderMail.php`)
   - Sends HTML emails with urgency-based styling
   - Supports different notification types (due, overdue, reminder, new project)
   - Includes project details and progress information

4. **Controller** (`app/Http/Controllers/NotificationController.php`)
   - Manages notification display and interactions
   - Handles marking as read (single and multiple)
   - Provides real-time notification updates via API
   - Supports notification deletion and clearing

5. **Views** (`resources/views/notifications.blade.php`)
   - Displays notifications with filtering and sorting
   - Shows notification statistics
   - Provides bulk actions (mark all read, delete, etc.)

6. **Scheduled Commands** (`routes/console.php`)
   - Hourly checks for project due dates
   - Morning check at 9:00 AM
   - Evening check at 6:00 PM

## Notification Types

- `project_due` - Project is due today
- `project_overdue` - Project is overdue
- `project_reminder` - General project reminder
- `project_due_soon` - Project due tomorrow
- `time_reminder` - Time tracking reminder
- `project_completed` - Project marked as completed
- `project_assigned` - User assigned to project
- `new_project` - New project created

## Urgency Levels

- `normal` - 3+ days remaining
- `moderate` - 2 days remaining
- `high` - 1 day remaining
- `critical` - Due today or overdue

## Email Configuration

The system uses Laravel's mail configuration. For production (Render.com):

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=MyTime
```

## Testing the System

### 1. Test Email Configuration
Visit: `/test-email`
- Sends a simple test email to verify SMTP configuration

### 2. Test Project Reminder Email
Visit: `/test-project-email`
- Sends a project due reminder email with sample project

### 3. Test Notifications
Visit: `/test-notifications`
- Creates sample notifications in the database

### 4. View All Notifications
Visit: `/notifications`
- View all notifications with filtering and management options

## API Endpoints

- `GET /notifications` - View all notifications
- `POST /notifications/mark-all-read` - Mark all as read
- `POST /notifications/mark-multiple-read` - Mark selected as read
- `POST /notifications/{id}/read` - Mark single as read
- `DELETE /notifications/{id}` - Delete notification
- `POST /notifications/clear-read` - Clear all read notifications
- `GET /notifications/unread-count` - Get unread count
- `GET /notifications/latest` - Get latest 5 notifications

## Scheduled Tasks

The system runs three scheduled checks:

1. **Hourly Check** - Every hour
   - Checks all projects for due dates
   - Sends notifications based on days remaining

2. **Morning Check** - 9:00 AM
   - Comprehensive check for all projects
   - Sends morning reminders for 3-day projects

3. **Evening Check** - 6:00 PM
   - Evening reminders for 3-day projects
   - Checks for any missed notifications

## User Preferences

Users can control notifications via profile preferences:
- `email_notifications` - Receive email notifications
- `project_updates` - Receive project update notifications
- `time_reminders` - Receive time tracking reminders
- `weekly_reports` - Receive weekly reports

## Troubleshooting

### Emails Not Sending
1. Check mail configuration in `.env`
2. Verify SMTP credentials
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with `/test-email` endpoint

### Notifications Not Appearing
1. Verify database migrations have run
2. Check if scheduled commands are running
3. Manually trigger: `php artisan projects:check-due-dates`
4. Check database for notification records

### Scheduled Commands Not Running
1. Ensure Laravel scheduler is running: `php artisan schedule:work`
2. On production (Render), ensure cron job is configured
3. Check Render logs for command execution

## Production Deployment

1. Set up mail configuration in Render environment variables
2. Configure database for session and queue storage
3. Set up cron job to run scheduler:
   ```
   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
   ```
4. Test email sending with `/test-email`
5. Monitor logs for any errors

## Future Enhancements

- [ ] Real-time notifications via WebSockets
- [ ] Notification preferences per project
- [ ] Notification templates customization
- [ ] Notification history and analytics
- [ ] SMS notifications
- [ ] Slack integration
- [ ] Notification digest (daily/weekly)
