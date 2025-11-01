# Quick Reference - Notification System

## What's New

### 1. Real-Time Notifications in Header
- Badge shows unread count
- Dropdown shows latest 5 notifications
- Auto-refresh every 30 seconds
- Click to mark as read

### 2. New Project Alerts
- Notification when project created
- Email sent to creator and team
- Appears in header immediately

### 3. Hourly Due Date Checks
- Runs every hour (8 AM - 9 PM)
- Sends reminders based on days remaining
- Creates notifications and emails

### 4. Enhanced Dashboard
- Upcoming due projects section
- Recent notifications section
- Auto-refresh every 60 seconds

## Files Changed

```
resources/views/layouts/app.blade.php          ← Real-time header
resources/views/dashboard.blade.php            ← Dashboard sections
app/Services/NotificationService.php           ← Enhanced error handling
routes/web.php                                 ← API endpoint
```

## How to Deploy

```bash
# 1. Commit code
git add .
git commit -m "Add real-time notifications and enhanced dashboard"
git push origin main

# 2. Render auto-deploys
# 3. Configure environment variables
# 4. Set up cron job
# 5. Test
```

## Test Endpoints

- `/test-email` - Test email configuration
- `/test-project-email` - Test project email
- `/test-notifications` - Create sample notifications
- `/notifications` - View all notifications
- `/api/upcoming-projects` - Get upcoming projects
- `/notifications/latest` - Get latest notifications
- `/notifications/unread-count` - Get unread count

## Key Features

| Feature | Trigger | Action |
|---------|---------|--------|
| New Project Alert | Project created | Notify creator & team |
| Due Reminder (3d) | 3 days before | Morning & evening |
| Due Reminder (2d) | 2 days before | Morning alert |
| Due Reminder (1d) | 1 day before | High alert |
| Due Today | Due date | Critical alert |
| Overdue | Past due date | Daily alert |

## Environment Variables

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

## Cron Job Setup

```
Schedule: * * * * *
Command: cd /opt/render/project/src && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Notifications not appearing | Check database, verify user relationship |
| Emails not sending | Verify SMTP credentials, test with `/test-email` |
| Scheduler not running | Verify cron job, check Render logs |
| Real-time updates not working | Check browser console, verify API endpoints |

## Documentation Files

- `NOTIFICATION_SYSTEM_COMPLETE.md` - Full documentation
- `NOTIFICATION_TESTING_GUIDE.md` - Testing guide
- `NOTIFICATION_SYSTEM_UPDATED.md` - Updated system docs
- `DEPLOYMENT_GUIDE_FINAL.md` - Deployment guide
- `NOTIFICATION_SYSTEM_SUMMARY.md` - Complete summary
- `QUICK_REFERENCE.md` - This file

## Next Steps

1. Push to GitHub
2. Render auto-deploys
3. Configure environment
4. Set up cron job
5. Test
6. Monitor logs

## Support

- Check logs: `storage/logs/laravel.log`
- Review documentation
- Test with provided endpoints
- Check Render dashboard

---

**Ready to deploy!** Push to GitHub and Render will handle the rest.
