# Render Deployment - Notification System Setup

## Pre-Deployment Checklist

- [ ] All code committed to GitHub
- [ ] Migrations created and tested locally
- [ ] Email configuration tested
- [ ] Notifications tested locally
- [ ] No sensitive data in code
- [ ] Environment variables documented

## Step 1: Push Code to GitHub

```bash
cd d:\Mytime
git add .
git commit -m "Improve notification system with better error handling and testing"
git push origin main
```

## Step 2: Configure Render Environment Variables

In Render Dashboard, add these environment variables:

### Mail Configuration
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

### Database Configuration
```
DB_CONNECTION=pgsql
DB_HOST=your-render-db-host
DB_PORT=5432
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### Application Configuration
```
APP_NAME=MyTime
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
LOG_CHANNEL=stderr
LOG_LEVEL=error
```

## Step 3: Set Up Cron Job for Scheduler

In Render Dashboard:

1. Go to your service
2. Add a new "Cron Job" service
3. Configure:
   - **Name:** mytime-scheduler
   - **Schedule:** `* * * * *` (every minute)
   - **Command:** `cd /opt/render/project/src && php artisan schedule:run >> /dev/null 2>&1`

Or add to `render.yaml`:

```yaml
services:
  - type: cron
    name: mytime-scheduler
    schedule: "* * * * *"
    command: "cd /opt/render/project/src && php artisan schedule:run >> /dev/null 2>&1"
```

## Step 4: Run Migrations on Render

After deployment:

1. Go to Render Dashboard
2. Open your service shell
3. Run:
   ```bash
   php artisan migrate --force
   ```

## Step 5: Test Email Configuration

1. SSH into Render service
2. Run:
   ```bash
   php artisan tinker
   >>> Mail::raw('Test email', function($m) { $m->to('your-email@gmail.com')->subject('Test'); });
   ```

## Step 6: Verify Notifications

1. Create a test project with end_date = tomorrow
2. Wait for scheduler to run (or manually trigger)
3. Check notifications in UI
4. Verify email received

## Troubleshooting on Render

### Check Logs
```bash
# In Render shell
tail -f /var/log/render/service.log
```

### Verify Scheduler Running
```bash
# In Render shell
ps aux | grep schedule
```

### Test Mail Configuration
```bash
# In Render shell
php artisan tinker
>>> config('mail.mailer')
>>> config('mail.from')
```

### Check Database Connection
```bash
# In Render shell
php artisan tinker
>>> DB::connection()->getPdo()
```

### View Recent Notifications
```bash
# In Render shell
php artisan tinker
>>> App\Models\Notification::latest()->limit(5)->get()
```

## Monitoring

### Set Up Log Monitoring
1. Go to Render Dashboard
2. Enable log streaming
3. Monitor for errors

### Check Scheduler Execution
```bash
# In Render shell
grep -i "schedule" /var/log/render/service.log
```

### Monitor Email Sending
```bash
# In Render shell
grep -i "email\|mail" /var/log/render/service.log
```

## Performance Optimization

### Database Indexes
Ensure these indexes exist:
```sql
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX idx_notifications_user_created ON notifications(user_id, created_at);
```

### Query Optimization
The notification queries use:
- Pagination (10 per page)
- Indexes on user_id and is_read
- Latest ordering

### Caching (Optional)
Consider adding caching for unread count:
```php
Cache::remember('user_' . $user->id . '_unread_count', 300, function() {
    return $user->notifications()->where('is_read', false)->count();
});
```

## Rollback Plan

If issues occur:

1. **Revert Code:**
   ```bash
   git revert HEAD
   git push origin main
   ```

2. **Render Auto-Redeploy:**
   - Render will automatically redeploy on push
   - Check deployment status in dashboard

3. **Disable Scheduler:**
   - Remove cron job from Render
   - Notifications will still work manually

4. **Check Logs:**
   - Review Render logs for errors
   - Check database for issues

## Success Indicators

✅ Deployment successful when:
- [ ] Application loads without errors
- [ ] Notifications page accessible
- [ ] Test email sends successfully
- [ ] Scheduler runs every minute
- [ ] Notifications created for due projects
- [ ] Emails sent to users
- [ ] No errors in logs
- [ ] Performance acceptable

## Post-Deployment

1. Monitor logs for 24 hours
2. Test with real projects
3. Verify emails received
4. Check notification accuracy
5. Gather user feedback
6. Document any issues
7. Plan improvements

## Support

For issues:
1. Check Render logs
2. Review Laravel logs
3. Test locally with same configuration
4. Check mail service status
5. Verify database connection
6. Review scheduled command output
