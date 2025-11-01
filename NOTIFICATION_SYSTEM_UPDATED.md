# MyTime Notification System - Complete Update

## Overview

The notification system has been completely updated with real-time notifications, new project creation alerts, hourly due date checks, and enhanced dashboard/header integration.

## Key Features

### 1. Real-Time Notifications in Header ✅
- **Location:** Top navigation bar
- **Features:**
  - Live notification badge showing unread count
  - Dropdown menu with latest 5 notifications
  - Auto-refresh every 30 seconds
  - Click to mark as read
  - Direct links to projects

### 2. New Project Creation Notifications ✅
- **Trigger:** When a new project is created
- **Recipients:** Project creator and all team members
- **Actions:**
  - In-app notification created
  - Email sent (if user has email notifications enabled)
  - Real-time update in header

### 3. Hourly Due Date Checks ✅
- **Schedule:** Every hour (8 AM - 9 PM)
- **Checks:**
  - 3 days before: Morning and evening reminders
  - 2 days before: Moderate alert
  - 1 day before: High alert
  - Due today: Critical alert
  - Overdue: Daily alerts
- **Actions:**
  - In-app notification created
  - Email sent (if enabled)
  - Real-time update in header

### 4. Enhanced Dashboard ✅
- **Upcoming Due Projects Section:**
  - Shows top 5 projects due soon
  - Color-coded badges (danger/warning/info)
  - Days remaining display
  - Direct links to projects
  - Auto-refresh every 60 seconds

- **Recent Notifications Section:**
  - Shows latest 5 notifications
  - Icon and color coding
  - Unread highlighting
  - Direct links to projects
  - Auto-refresh every 60 seconds

### 5. Email Notifications ✅
- **Triggers:**
  - New project assignment
  - Project due reminders (3, 2, 1 days before, today, overdue)
  - Project completion
  - Time tracking reminders

- **Features:**
  - Urgency-based subject lines
  - Professional HTML design
  - Project details included
  - Progress bar visualization
  - Call-to-action buttons

## System Architecture

### Database Schema
```
notifications table:
- id (primary key)
- user_id (foreign key)
- project_id (foreign key, nullable)
- type (string)
- title (string)
- message (text)
- data (json)
- is_read (boolean)
- read_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

Indexes:
- (user_id, is_read) - for quick unread count
- (user_id, created_at) - for pagination
```

### Models

**Notification Model** (`app/Models/Notification.php`)
- Relationships: user(), project()
- Methods: markAsRead()
- Attributes: icon, color

**User Model** (`app/Models/User.php`)
- Relationship: notifications()
- Preferences: email_notifications, project_updates, time_reminders

**Project Model** (`app/Models/Project.php`)
- Method: sendNewProjectNotification()
- Triggered on: model creation

### Services

**NotificationService** (`app/Services/NotificationService.php`)
- createNotification() - Create in-app notification
- sendProjectDueReminder() - Send due date reminder with email
- checkProjectDueDates() - Hourly check for due projects
- notifyProjectAssignment() - Notify on project assignment
- notifyProjectCompletion() - Notify on project completion
- sendTimeTrackingReminder() - Send time tracking reminder

### Controllers

**NotificationController** (`app/Http/Controllers/NotificationController.php`)
- index() - Display all notifications
- markAllRead() - Mark all as read
- markAsRead() - Mark single as read
- markMultipleAsRead() - Mark multiple as read
- destroy() - Delete notification
- clearRead() - Clear all read notifications
- getUnreadCount() - Get unread count (API)
- getLatest() - Get latest 5 notifications (API)

### Views

**Header** (`resources/views/layouts/app.blade.php`)
- Real-time notification dropdown
- Auto-refresh every 30 seconds
- Mark as read on click
- Unread badge

**Dashboard** (`resources/views/dashboard.blade.php`)
- Upcoming due projects section
- Recent notifications section
- Auto-refresh every 60 seconds
- Color-coded badges

**Notifications Page** (`resources/views/notifications.blade.php`)
- Full notification list with pagination
- Statistics cards
- Bulk actions
- Filtering and sorting

### Routes

**API Routes:**
- `GET /api/upcoming-projects` - Get upcoming due projects
- `GET /notifications/latest` - Get latest 5 notifications
- `GET /notifications/unread-count` - Get unread count

**Web Routes:**
- `GET /notifications` - View all notifications
- `POST /notifications/mark-all-read` - Mark all as read
- `POST /notifications/mark-multiple-read` - Mark multiple as read
- `POST /notifications/{id}/read` - Mark single as read
- `DELETE /notifications/{id}` - Delete notification
- `POST /notifications/clear-read` - Clear read notifications

**Test Routes:**
- `GET /test-email` - Test email configuration
- `GET /test-project-email` - Test project email
- `GET /test-notifications` - Create sample notifications

### Scheduled Commands

**Console Routes** (`routes/console.php`)
- Hourly: `projects:check-due-dates`
- Daily 9:00 AM: `projects:check-due-dates`
- Daily 6:00 PM: `projects:check-due-dates`

## Notification Types

| Type | Icon | Color | Trigger |
|------|------|-------|---------|
| project_due | fa-calendar-check | warning | Project due today |
| project_overdue | fa-exclamation-triangle | danger | Project overdue |
| project_reminder | fa-bell | info | 3, 2 days before |
| project_due_soon | fa-bell | info | 1 day before |
| time_reminder | fa-clock | primary | Time tracking |
| project_completed | fa-check-circle | success | Project completed |
| project_assigned | fa-user-plus | success | Project assigned |
| new_project | fa-folder-plus | primary | New project created |

## User Preferences

Users can control notifications via profile preferences:
- `email_notifications` - Receive email notifications
- `project_updates` - Receive project update notifications
- `time_reminders` - Receive time tracking reminders
- `weekly_reports` - Receive weekly reports

## Real-Time Updates

### Header Notifications
- **Polling Interval:** 30 seconds
- **Triggers:**
  - Page load
  - Dropdown click
  - Automatic refresh

### Dashboard Sections
- **Polling Interval:** 60 seconds
- **Sections:**
  - Upcoming due projects
  - Recent notifications

### Mark as Read
- **Trigger:** Click on notification in dropdown
- **Action:** AJAX POST to `/notifications/{id}/read`
- **Response:** Badge count updated

## Email Configuration

### Required Environment Variables
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

### Email Templates
- `resources/views/emails/project-due-reminder.blade.php`
  - Urgency-based styling
  - Project details
  - Progress bar
  - Call-to-action button

## Testing

### Test Endpoints
1. **Test Email:** `/test-email`
   - Sends simple test email
   - Verifies SMTP configuration

2. **Test Project Email:** `/test-project-email`
   - Sends project reminder email
   - Uses first project in database

3. **Test Notifications:** `/test-notifications`
   - Creates sample notifications
   - Tests notification creation

### Manual Testing
1. Create a new project
2. Verify notification appears in header
3. Check email received
4. View dashboard for upcoming projects
5. Check notification page

## Performance Optimization

### Database Indexes
- `(user_id, is_read)` - Quick unread count
- `(user_id, created_at)` - Pagination

### Caching Opportunities
- Unread count (cache 5 minutes)
- Latest notifications (cache 1 minute)
- Upcoming projects (cache 5 minutes)

### Query Optimization
- Pagination (10 per page)
- Limit latest to 5
- Eager loading relationships

## Security

### Authorization
- Users can only see their own notifications
- Users can only modify their own notifications
- CSRF protection on all POST/DELETE routes

### Data Validation
- Input validation on all endpoints
- Type checking for notification types
- User existence verification

### Error Handling
- Try-catch blocks for email sending
- Proper error logging
- User-friendly error messages

## Logging

### Log Locations
- **Main Log:** `storage/logs/laravel.log`
- **Search Terms:**
  - "notification" - Notification events
  - "email" or "mail" - Email events
  - "ERROR" - Error messages

### Log Examples
```
[2024-01-15 10:30:45] local.INFO: In-app notification created for user 1: 📅 Morning Reminder: Project Due in 3 Days
[2024-01-15 10:30:46] local.INFO: Email sent to user@example.com for project Test Project (Days remaining: 3)
[2024-01-15 10:30:47] local.ERROR: Failed to send project due reminder email to user@example.com: SMTP error
```

## Deployment Checklist

- [ ] Database migrations run
- [ ] Mail configuration set in environment
- [ ] Scheduler configured to run
- [ ] Test email sending
- [ ] Test notification creation
- [ ] Monitor logs for errors
- [ ] Verify emails received
- [ ] Check notification display
- [ ] Test real-time updates
- [ ] Verify dashboard sections load

## Troubleshooting

### Notifications Not Appearing
1. Check database: `SELECT COUNT(*) FROM notifications;`
2. Verify user relationship: `SELECT * FROM notifications WHERE user_id = 1;`
3. Check logs for errors
4. Test with `/test-notifications`

### Emails Not Sending
1. Verify mail configuration: `php artisan tinker` → `config('mail.mailer')`
2. Test with `/test-email`
3. Check SMTP credentials
4. Review logs for errors

### Real-Time Updates Not Working
1. Check browser console for errors
2. Verify API endpoints are accessible
3. Check CSRF token in meta tag
4. Verify user is authenticated

### Scheduled Commands Not Running
1. Verify scheduler is running: `php artisan schedule:work`
2. Check cron job on production
3. Review logs for command execution
4. Test manually: `php artisan projects:check-due-dates`

## Future Enhancements

- [ ] WebSocket real-time notifications
- [ ] Notification preferences per project
- [ ] Custom notification templates
- [ ] Notification history and analytics
- [ ] SMS notifications
- [ ] Slack integration
- [ ] Notification digest (daily/weekly)
- [ ] Push notifications
- [ ] Notification scheduling

## Summary

The notification system is now fully integrated with:
- ✅ Real-time header notifications
- ✅ New project creation alerts
- ✅ Hourly due date checks
- ✅ Enhanced dashboard with upcoming projects
- ✅ Email notifications
- ✅ User preferences
- ✅ Comprehensive logging
- ✅ Error handling
- ✅ Security measures

**Ready for production deployment!**
