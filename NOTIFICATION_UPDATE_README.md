# Email Notification System Update

## Overview
The email notification system has been updated to send notifications for **all priority levels** with a special focus on **urgent projects** that start sending notifications at **5 days** before the due date.

## Notification Schedule by Priority

### 🚨 Urgent Priority
- **5 days** before due date
- **3 days** before due date
- **2 days** before due date
- **1 day** before due date
- **On due date** (day 0)
- **When overdue** (continues daily)

### 🔴 High Priority
- **7 days** before due date
- **3 days** before due date
- **1 day** before due date
- **On due date** (day 0)
- **When overdue** (continues daily)

### 🟡 Medium Priority
- **7 days** before due date
- **3 days** before due date
- **On due date** (day 0)
- **When overdue** (continues daily)

### 🟢 Low Priority
- **7 days** before due date
- **On due date** (day 0)
- **When overdue** (continues daily)

## Email Features

### Priority-Based Styling
The email template now includes dynamic styling based on project priority:

1. **Header Colors**:
   - Urgent: Red gradient
   - High: Orange gradient
   - Medium: Yellow gradient
   - Low: Green gradient

2. **Priority Badges**: Visual badges showing the priority level with color coding

3. **Urgent Alerts**: Special messaging for urgent priority projects

4. **Enhanced Alerts**: Different alert styles based on urgency and days remaining

## How It Works

### Automated Checks
The system runs the `projects:check-due-dates` command which:
1. Fetches all active projects (not completed or cancelled)
2. Calculates days remaining until due date
3. Determines if notification should be sent based on priority level
4. Sends both in-app and email notifications to:
   - Project creator
   - All team members (if they have project updates enabled)

### Email Notifications
Users receive email notifications only if:
- They have `email_notifications` enabled in their profile
- They are either the project creator or a team member
- Team members must have `project_updates` enabled

## Setting Up Automated Notifications

### Option 1: Laravel Scheduler (Recommended)
Add to your server's crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Then in `routes/console.php` or `app/Console/Kernel.php`, schedule the command:
```php
$schedule->command('projects:check-due-dates')->daily();
```

### Option 2: Manual Cron Job
Add to crontab to run daily at 9 AM:
```bash
0 9 * * * cd /path-to-your-project && php artisan projects:check-due-dates
```

### Option 3: Manual Testing
Run manually for testing:
```bash
php artisan projects:check-due-dates
```

## Email Content

Each notification email includes:
- **Priority-colored header** with project urgency indicator
- **Alert message** showing days remaining or overdue status
- **Project details**:
  - Project name
  - Status
  - Priority (with badge)
  - Start date
  - Due date
  - Progress percentage
- **Visual progress bar**
- **Project description** (if available)
- **Direct link** to view project details
- **Actionable message** based on project status

## Benefits

1. **Priority-Aware**: Different notification frequencies based on project importance
2. **Urgent Focus**: Urgent projects get more frequent reminders (starting at 5 days)
3. **Comprehensive Coverage**: All priority levels receive appropriate notifications
4. **Visual Clarity**: Color-coded emails make priority immediately obvious
5. **Actionable**: Direct links to project details for quick access
6. **Flexible**: Users can control notifications via their profile settings

## User Settings

Users can control notifications through their profile:
- **Email Notifications**: Enable/disable all email notifications
- **Project Updates**: Enable/disable project-related notifications (for team members)

## Testing

To test the notification system:

1. Create projects with different priorities and due dates
2. Run the command manually:
   ```bash
   php artisan projects:check-due-dates
   ```
3. Check your email for notifications
4. Verify the styling matches the priority level

## Troubleshooting

If emails are not being sent:
1. Check `.env` file for correct mail configuration
2. Verify user has `email_notifications` enabled
3. Check Laravel logs at `storage/logs/laravel.log`
4. Test mail configuration: `php artisan tinker` then `Mail::raw('Test', function($msg) { $msg->to('your@email.com')->subject('Test'); });`

## Files Modified

1. **app/Services/NotificationService.php**: Updated `checkProjectDueDates()` method with priority-based logic
2. **resources/views/emails/project-due-reminder.blade.php**: Enhanced email template with priority styling and badges

## Future Enhancements

Potential improvements:
- Customizable notification schedules per user
- SMS notifications for urgent projects
- Slack/Teams integration
- Notification digest (daily summary)
- Snooze functionality for reminders
