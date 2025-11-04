# Notification System - Quick Test Guide

## Quick Start Testing

### Step 1: Verify Installation
All files have been created. Check these files exist:
- ✅ `app/Console/Commands/CheckProjectDueDates.php`
- ✅ `app/Console/Kernel.php`
- ✅ `app/Events/ProjectCreated.php`
- ✅ `app/Events/ProjectAssigned.php`
- ✅ `app/Events/ProjectCompleted.php`
- ✅ `app/Listeners/SendProjectCreatedNotification.php`
- ✅ `app/Listeners/SendProjectAssignedNotification.php`
- ✅ `app/Listeners/SendProjectCompletedNotification.php`

### Step 2: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 3: Test Project Creation
1. Log in as admin
2. Go to Projects → Add Project
3. Fill in:
   - Name: "Test Project"
   - Description: "Testing notifications"
   - Status: "active"
   - Priority: "high"
   - Start Date: Today
   - End Date: 3 days from now
   - Add team members (select at least one user)
4. Click "Create Project"

### Step 4: Check Notifications
1. Click the bell icon in the header
2. You should see:
   - "✨ New Project Created" notification
   - "👥 New Project Assignment" notifications for each team member

3. Or go to Notifications page to see full list

### Step 5: Test Due Date Reminders
Run the command manually:
```bash
php artisan projects:check-due-dates
```

You should see notifications created for:
- Projects due in 3 days
- Projects due in 2 days
- Projects due in 1 day
- Projects due today
- Overdue projects

### Step 6: Test Project Completion
1. Go to a project
2. Mark it as complete
3. Check notifications - you should see completion notifications

### Step 7: Test Notification Actions
1. Click on a notification in the dropdown
2. It should mark as read
3. Badge count should decrease
4. Go to Notifications page
5. Test:
   - Mark as Read button
   - Mark Selected button
   - Delete button
   - Clear Read button

---

## Expected Results

### After Creating Project
- Notification count badge shows in header
- Dropdown shows latest notifications
- Notifications page shows all notifications
- Each notification has:
  - Icon (✨, 👥, ✅, etc.)
  - Title
  - Message
  - Time (e.g., "2 minutes ago")
  - Color coding

### After Running Due Date Check
- Notifications created for upcoming projects
- Different messages based on days remaining:
  - 3 days: "📅 Morning Reminder: Project Due in 3 Days"
  - 2 days: "⚠️ Moderate Alert: Project Due in 2 Days"
  - 1 day: "🚨 HIGH ALERT: Project Due Tomorrow!"
  - 0 days: "🔴 CRITICAL: Project Due TODAY!"
  - Negative: "❌ OVERDUE: Project Deadline Passed!"

### After Completing Project
- Completion notifications appear
- All team members see the notification
- Notification shows project name

---

## Database Verification

### Check Notifications in Database
```bash
php artisan tinker

# Count all notifications
>>> \App\Models\Notification::count()

# See recent notifications
>>> \App\Models\Notification::latest()->limit(5)->get()

# See notifications for specific user
>>> \App\Models\User::find(1)->notifications()->get()

# Count unread
>>> \App\Models\Notification::where('is_read', false)->count()
```

---

## Troubleshooting

### No Notifications Appearing
1. Check if events are firing:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for: "Notification sent to"

2. Check if listeners are registered:
   ```bash
   php artisan event:list
   ```

3. Verify EventServiceProvider:
   ```bash
   php artisan tinker
   >>> event(new \App\Events\ProjectCreated(\App\Models\Project::first()))
   ```

### Scheduler Not Running
1. Run manually:
   ```bash
   php artisan projects:check-due-dates
   ```

2. Check if command exists:
   ```bash
   php artisan list | grep projects
   ```

3. Run scheduler in foreground:
   ```bash
   php artisan schedule:work
   ```

### Notifications Not Showing in UI
1. Refresh the page
2. Check browser console for errors
3. Verify notification dropdown JavaScript is working
4. Check if notifications table has records

---

## Manual Testing Commands

### Create Test Notification
```bash
php artisan tinker

# Create a notification manually
>>> $user = \App\Models\User::first();
>>> $project = \App\Models\Project::first();
>>> \App\Models\Notification::create([
    'user_id' => $user->id,
    'project_id' => $project->id,
    'type' => 'test',
    'title' => 'Test Notification',
    'message' => 'This is a test notification',
    'is_read' => false
]);
```

### Dispatch Event Manually
```bash
php artisan tinker

>>> $project = \App\Models\Project::first();
>>> event(new \App\Events\ProjectCreated($project));
```

### Run Due Date Check
```bash
php artisan projects:check-due-dates
```

### Check Notification Count
```bash
php artisan tinker
>>> \App\Models\Notification::count()
```

---

## Performance Testing

### Check Query Performance
```bash
php artisan tinker

>>> \DB::enableQueryLog();
>>> $notifications = \App\Models\User::find(1)->notifications()->latest()->paginate(10);
>>> \DB::getQueryLog();
```

### Monitor Scheduler
```bash
php artisan schedule:work
```

This will show:
- When commands run
- How long they take
- Any errors

---

## Success Indicators

✅ **System is working if:**
1. Notifications appear after creating project
2. Notification dropdown shows count badge
3. Notifications page displays all notifications
4. Mark as read functionality works
5. Due date check creates notifications
6. Completion notifications appear
7. Database has notification records
8. Logs show "Notification sent" messages

❌ **System has issues if:**
1. No notifications appear after project creation
2. Notification count stays at 0
3. Dropdown shows "No new notifications"
4. Database notifications table is empty
5. Logs show errors
6. Scheduler doesn't run

---

## Next Steps

Once testing is complete:
1. ✅ Verify all notifications work
2. ✅ Test with multiple users
3. ✅ Test with different project dates
4. ✅ Monitor logs for errors
5. ✅ Set up production scheduler (cron)
6. ✅ Configure email notifications (optional)
7. ✅ Set up push notifications (optional)

---

## Support

If notifications aren't working:
1. Check `storage/logs/laravel.log` for errors
2. Verify all files were created
3. Run `php artisan cache:clear`
4. Run `php artisan config:clear`
5. Check database for notification records
6. Verify EventServiceProvider is registered
7. Test with manual commands above

**The notification system is now fully implemented and ready to use!**
