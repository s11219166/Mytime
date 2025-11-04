# Quick Commands for Testing Notifications

## Clear Cache & Config
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Test Notification Creation
```bash
php artisan tinker

# Count all notifications
>>> \App\Models\Notification::count()

# See recent notifications
>>> \App\Models\Notification::latest()->limit(5)->get()

# See notifications for user 1
>>> \App\Models\User::find(1)->notifications()->get()

# Count unread
>>> \App\Models\Notification::where('is_read', false)->count()

# Create test notification
>>> \App\Models\Notification::create([
    'user_id' => 1,
    'project_id' => 1,
    'type' => 'test',
    'title' => 'Test',
    'message' => 'Test notification',
    'is_read' => false
]);
```

## Test Events
```bash
php artisan tinker

# Dispatch ProjectCreated event
>>> $project = \App\Models\Project::first();
>>> event(new \App\Events\ProjectCreated($project));

# Dispatch ProjectCompleted event
>>> event(new \App\Events\ProjectCompleted($project));

# Dispatch ProjectAssigned event
>>> $user = \App\Models\User::find(2);
>>> event(new \App\Events\ProjectAssigned($project, $user));
```

## Test Scheduler
```bash
# Run due date check manually
php artisan projects:check-due-dates

# Run scheduler in foreground (shows when commands run)
php artisan schedule:work

# List all commands
php artisan list | grep projects
```

## Check Event Registration
```bash
php artisan tinker

# List all events
>>> event(new \App\Events\ProjectCreated(\App\Models\Project::first()));

# Check if listeners are registered
>>> \Illuminate\Support\Facades\Event::listen('App\Events\ProjectCreated', 'App\Listeners\SendProjectCreatedNotification');
```

## Database Queries
```bash
php artisan tinker

# Enable query logging
>>> \DB::enableQueryLog();

# Run a query
>>> $notifications = \App\Models\User::find(1)->notifications()->latest()->paginate(10);

# See queries
>>> \DB::getQueryLog();

# Count notifications
>>> \App\Models\Notification::count()

# See all notifications
>>> \App\Models\Notification::all()

# See unread
>>> \App\Models\Notification::where('is_read', false)->get()

# See by type
>>> \App\Models\Notification::where('type', 'new_project')->get()

# See by user
>>> \App\Models\Notification::where('user_id', 1)->get()

# See by project
>>> \App\Models\Notification::where('project_id', 1)->get()
```

## Check Logs
```bash
# View last 50 lines
tail -50 storage/logs/laravel.log

# Follow logs in real-time
tail -f storage/logs/laravel.log

# Search for notifications
grep "Notification" storage/logs/laravel.log

# Search for errors
grep "Error" storage/logs/laravel.log

# Count notifications in logs
grep -c "Notification sent" storage/logs/laravel.log
```

## Test API Endpoints
```bash
# Get unread count
curl http://localhost:8000/notifications/unread-count

# Get latest notifications
curl http://localhost:8000/notifications/latest

# Mark as read (requires POST)
curl -X POST http://localhost:8000/notifications/1/read \
  -H "X-CSRF-TOKEN: your-token"

# Delete notification (requires DELETE)
curl -X DELETE http://localhost:8000/notifications/1 \
  -H "X-CSRF-TOKEN: your-token"
```

## File Verification
```bash
# Check if files exist
ls -la app/Console/Commands/CheckProjectDueDates.php
ls -la app/Console/Kernel.php
ls -la app/Events/ProjectCreated.php
ls -la app/Events/ProjectAssigned.php
ls -la app/Events/ProjectCompleted.php
ls -la app/Listeners/SendProjectCreatedNotification.php
ls -la app/Listeners/SendProjectAssignedNotification.php
ls -la app/Listeners/SendProjectCompletedNotification.php

# Check if all files exist
find app -name "*Notification*" -o -name "*ProjectCreated*" -o -name "*ProjectAssigned*" -o -name "*ProjectCompleted*"
```

## Performance Testing
```bash
php artisan tinker

# Time a query
>>> $start = microtime(true);
>>> $notifications = \App\Models\User::find(1)->notifications()->latest()->paginate(10);
>>> $end = microtime(true);
>>> echo ($end - $start) . " seconds";

# Check query count
>>> \DB::enableQueryLog();
>>> $notifications = \App\Models\User::find(1)->notifications()->latest()->paginate(10);
>>> count(\DB::getQueryLog());
```

## Troubleshooting
```bash
# Check if command exists
php artisan list | grep check-due-dates

# Check if events are registered
php artisan event:list

# Check if listeners are registered
php artisan event:list

# Check configuration
php artisan config:show

# Check database connection
php artisan tinker
>>> \DB::connection()->getPdo()

# Check if table exists
>>> \Illuminate\Support\Facades\Schema::hasTable('notifications')

# Check table structure
>>> \Illuminate\Support\Facades\Schema::getColumns('notifications')
```

## Quick Test Workflow
```bash
# 1. Clear cache
php artisan cache:clear

# 2. Check if files exist
ls -la app/Console/Commands/CheckProjectDueDates.php

# 3. Test event dispatch
php artisan tinker
>>> event(new \App\Events\ProjectCreated(\App\Models\Project::first()));

# 4. Check if notification created
>>> \App\Models\Notification::latest()->first()

# 5. Run due date check
php artisan projects:check-due-dates

# 6. Check notifications again
>>> \App\Models\Notification::count()

# 7. Exit tinker
>>> exit
```

## Production Deployment
```bash
# 1. Deploy code
git pull origin main

# 2. Clear cache
php artisan cache:clear
php artisan config:clear

# 3. Run migrations (if any)
php artisan migrate

# 4. Set up cron job
# Add to crontab:
# * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1

# 5. Verify scheduler
php artisan schedule:list

# 6. Monitor logs
tail -f storage/logs/laravel.log
```

## Common Issues & Fixes
```bash
# Issue: No notifications appearing
# Fix 1: Clear cache
php artisan cache:clear

# Fix 2: Check if events are firing
tail -f storage/logs/laravel.log

# Fix 3: Manually dispatch event
php artisan tinker
>>> event(new \App\Events\ProjectCreated(\App\Models\Project::first()));

# Issue: Scheduler not running
# Fix 1: Run manually
php artisan projects:check-due-dates

# Fix 2: Check if command exists
php artisan list | grep check-due-dates

# Fix 3: Run scheduler in foreground
php artisan schedule:work

# Issue: Database errors
# Fix 1: Check connection
php artisan tinker
>>> \DB::connection()->getPdo()

# Fix 2: Check table exists
>>> \Illuminate\Support\Facades\Schema::hasTable('notifications')

# Fix 3: Run migrations
php artisan migrate
```

## Useful Aliases
```bash
# Add to .bashrc or .zshrc for quick access

alias artisan='php artisan'
alias tinker='php artisan tinker'
alias migrate='php artisan migrate'
alias cache-clear='php artisan cache:clear && php artisan config:clear'
alias check-notifications='php artisan projects:check-due-dates'
alias schedule-work='php artisan schedule:work'
alias logs='tail -f storage/logs/laravel.log'
```

## One-Liners
```bash
# Count notifications
php artisan tinker --execute="echo \App\Models\Notification::count();"

# Get latest notification
php artisan tinker --execute="echo \App\Models\Notification::latest()->first();"

# Check unread count
php artisan tinker --execute="echo \App\Models\Notification::where('is_read', false)->count();"

# Run due date check
php artisan projects:check-due-dates

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Check if scheduler is working
php artisan schedule:list
```

## Monitoring
```bash
# Watch logs in real-time
watch -n 1 'tail -20 storage/logs/laravel.log'

# Count notifications every 5 seconds
watch -n 5 'php artisan tinker --execute="echo \App\Models\Notification::count();"'

# Monitor scheduler
php artisan schedule:work

# Check database size
php artisan tinker
>>> \DB::table('notifications')->count()
>>> \DB::table('notifications')->sum('id')
```

---

## Quick Reference

| Task | Command |
|------|---------|
| Clear cache | `php artisan cache:clear` |
| Test event | `php artisan tinker` then `event(new \App\Events\ProjectCreated(...))` |
| Run due date check | `php artisan projects:check-due-dates` |
| Count notifications | `php artisan tinker --execute="echo \App\Models\Notification::count();"` |
| View logs | `tail -f storage/logs/laravel.log` |
| Run scheduler | `php artisan schedule:work` |
| Check files | `ls -la app/Console/Commands/CheckProjectDueDates.php` |
| Test API | `curl http://localhost:8000/notifications/latest` |

---

**Use these commands to test and verify the notification system is working!**
