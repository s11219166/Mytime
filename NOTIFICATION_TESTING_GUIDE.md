# Notification System Testing Guide

## Quick Start Testing

### 1. Test Email Configuration
**URL:** `http://localhost:8000/test-email`

This sends a simple test email to verify your SMTP configuration is working.

**Expected Result:**
- Success message displayed
- Email received in inbox
- Check logs: `storage/logs/laravel.log`

### 2. Test Project Reminder Email
**URL:** `http://localhost:8000/test-project-email`

This sends a project due reminder email with a sample project.

**Expected Result:**
- Success message displayed
- Email received with project details
- Email includes urgency indicators

### 3. Test In-App Notifications
**URL:** `http://localhost:8000/test-notifications`

This creates sample notifications in the database.

**Expected Result:**
- Redirects to notifications page
- Sample notifications appear in the list
- Notifications show different types and urgency levels

### 4. View All Notifications
**URL:** `http://localhost:8000/notifications`

View all notifications with management options.

**Features to Test:**
- [ ] Notifications display correctly
- [ ] Unread count is accurate
- [ ] Mark as read functionality works
- [ ] Delete functionality works
- [ ] Mark all read works
- [ ] Clear read notifications works
- [ ] Pagination works

## Manual Testing Checklist

### Database Setup
- [ ] Run migrations: `php artisan migrate`
- [ ] Verify notifications table exists
- [ ] Check table structure with proper indexes

### Email Configuration
- [ ] Set up SMTP credentials in `.env`
- [ ] Test with `/test-email` endpoint
- [ ] Verify email appears in inbox
- [ ] Check email formatting

### Notification Creation
- [ ] Create a new project
- [ ] Verify notification created for creator
- [ ] Verify notification created for team members
- [ ] Check notification appears in UI

### Scheduled Commands
- [ ] Run manually: `php artisan projects:check-due-dates`
- [ ] Check logs for output
- [ ] Verify notifications created for due projects
- [ ] Test with projects at different due dates

### User Preferences
- [ ] Disable email notifications in profile
- [ ] Create notification and verify no email sent
- [ ] Enable email notifications
- [ ] Create notification and verify email sent

## Testing Different Scenarios

### Scenario 1: Project Due in 3 Days
1. Create a project with end_date = today + 3 days
2. Run: `php artisan projects:check-due-dates`
3. Verify:
   - [ ] In-app notification created
   - [ ] Email sent (if enabled)
   - [ ] Notification type is 'project_reminder'
   - [ ] Title includes "3 Days"

### Scenario 2: Project Due Tomorrow
1. Create a project with end_date = tomorrow
2. Run: `php artisan projects:check-due-dates`
3. Verify:
   - [ ] In-app notification created
   - [ ] Email sent with HIGH ALERT
   - [ ] Notification type is 'project_due_soon'
   - [ ] Title includes "Tomorrow"

### Scenario 3: Project Due Today
1. Create a project with end_date = today
2. Run: `php artisan projects:check-due-dates`
3. Verify:
   - [ ] In-app notification created
   - [ ] Email sent with CRITICAL ALERT
   - [ ] Notification type is 'project_due'
   - [ ] Title includes "TODAY"

### Scenario 4: Project Overdue
1. Create a project with end_date = yesterday
2. Run: `php artisan projects:check-due-dates`
3. Verify:
   - [ ] In-app notification created
   - [ ] Email sent with OVERDUE alert
   - [ ] Notification type is 'project_overdue'
   - [ ] Title includes "OVERDUE"

### Scenario 5: Project Assignment
1. Create a new project with team members
2. Verify:
   - [ ] Notifications created for all team members
   - [ ] Notification type is 'project_assigned'
   - [ ] Emails sent to all team members (if enabled)

## API Testing

### Get Unread Count
```bash
curl -X GET http://localhost:8000/notifications/unread-count \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Latest Notifications
```bash
curl -X GET http://localhost:8000/notifications/latest \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Mark All as Read
```bash
curl -X POST http://localhost:8000/notifications/mark-all-read \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Mark Single as Read
```bash
curl -X POST http://localhost:8000/notifications/1/read \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Delete Notification
```bash
curl -X DELETE http://localhost:8000/notifications/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

## Troubleshooting

### Emails Not Sending

**Check 1: Mail Configuration**
```bash
php artisan tinker
>>> config('mail.mailer')
>>> config('mail.from')
```

**Check 2: SMTP Credentials**
- Verify MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD in .env
- Test with `/test-email` endpoint

**Check 3: Logs**
```bash
tail -f storage/logs/laravel.log
```

**Check 4: User Preferences**
- Verify user has `email_notifications` enabled
- Check in database: `SELECT email_notifications FROM users WHERE id = 1;`

### Notifications Not Appearing

**Check 1: Database**
```bash
php artisan tinker
>>> App\Models\Notification::count()
>>> App\Models\Notification::latest()->first()
```

**Check 2: Scheduled Commands**
```bash
php artisan schedule:work
```

**Check 3: Manual Trigger**
```bash
php artisan projects:check-due-dates
```

**Check 4: Logs**
```bash
grep -i notification storage/logs/laravel.log
```

### Scheduled Commands Not Running

**Local Development:**
```bash
php artisan schedule:work
```

**Production (Render):**
1. Add cron job to Render environment
2. Verify in Render logs
3. Check command output

## Performance Testing

### Load Test Notifications
```bash
php artisan tinker
>>> for ($i = 0; $i < 1000; $i++) {
    App\Models\Notification::create([
        'user_id' => 1,
        'type' => 'test',
        'title' => 'Test ' . $i,
        'message' => 'Test message',
    ]);
}
```

### Check Query Performance
```bash
php artisan tinker
>>> DB::enableQueryLog()
>>> App\Models\User::find(1)->notifications()->latest()->paginate(10)
>>> dd(DB::getQueryLog())
```

## Success Criteria

✅ All tests pass when:
- [ ] Emails send successfully
- [ ] Notifications appear in database
- [ ] Notifications display in UI
- [ ] Mark as read works
- [ ] Delete works
- [ ] Scheduled commands run
- [ ] User preferences respected
- [ ] No errors in logs
- [ ] Performance acceptable

## Next Steps

1. Deploy to Render
2. Configure mail service
3. Set up cron job for scheduler
4. Monitor logs for errors
5. Test with real users
6. Gather feedback
7. Iterate and improve
