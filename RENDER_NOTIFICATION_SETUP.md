# Notification System - Render Deployment Setup

## ✅ System Adapted for Render

The notification system has been updated to work with Render's deployment environment. Since Render doesn't support traditional cron jobs, the system now uses **manual web triggers** instead.

---

## 🔧 Key Changes for Render

### 1. **Disabled Queuing**
- Event listeners no longer use `ShouldQueue`
- Notifications are processed synchronously
- No queue workers needed

### 2. **Manual Triggers Instead of Cron**
- **Cron jobs** → **Web API endpoints**
- **Scheduled tasks** → **Manual button clicks**
- **Automatic checks** → **On-demand execution**

### 3. **Web-Based Due Date Checks**
- Added `NotificationTriggerController`
- Manual trigger endpoints
- UI buttons for testing

---

## 🚀 How to Test on Render

### Step 1: Deploy to Render
Make sure your code is deployed to Render with all the new files.

### Step 2: Create a Project
1. Log in as admin
2. Go to Projects → Add Project
3. Fill in details and add team members
4. Click "Create Project"

**Expected**: You should see notifications in the notification panel

### Step 3: Check Notifications
1. Click the bell icon in the header
2. Or go to Notifications page
3. You should see:
   - "✨ New Project Created" for the creator
   - "👥 New Project Assignment" for each team member

### Step 4: Use Manual Triggers
1. Go to Notifications page
2. Click the "Tools" dropdown button
3. Try each option:

#### A. Check Due Dates
- Click "Check Due Dates"
- This manually runs the due date check
- Creates notifications for projects with upcoming deadlines

#### B. Create Test Notifications
- Click "Create Test Notifications"
- Creates sample notifications for testing
- Useful for debugging

#### C. View Stats
- Click "View Stats"
- Shows notification statistics
- Displays counts and project information

---

## 📡 API Endpoints for External Triggers

If you want to trigger notifications from external services (like cron-job services), use these endpoints:

### Check Due Dates
```bash
POST /notifications/trigger/check-due-dates
Authorization: Bearer {your-token} or logged in user
```

### Create Test Notifications
```bash
POST /notifications/trigger/test
Authorization: Bearer {your-token} or logged in user
```

### Get Stats
```bash
GET /notifications/stats
Authorization: Bearer {your-token} or logged in user
```

---

## 🔄 Setting Up External Cron (Optional)

If you want automated checks, you can use external cron services:

### Option 1: Cron-Job.org
1. Sign up at cron-job.org
2. Create a new cron job
3. URL: `https://your-render-app.com/notifications/trigger/check-due-dates`
4. Method: POST
5. Headers: `X-CSRF-TOKEN: your-csrf-token`
6. Schedule: Every 4 hours or daily

### Option 2: GitHub Actions
Create `.github/workflows/check-due-dates.yml`:
```yaml
name: Check Due Dates
on:
  schedule:
    - cron: '0 */4 * * *'  # Every 4 hours
jobs:
  check-due-dates:
    runs-on: ubuntu-latest
    steps:
      - name: Check due dates
        run: |
          curl -X POST \
            https://your-render-app.com/notifications/trigger/check-due-dates \
            -H "X-CSRF-TOKEN: your-csrf-token"
```

### Option 3: Render Cron (if available)
If Render adds cron support, you can re-enable the Laravel scheduler.

---

## 🧪 Testing Checklist

### Basic Functionality
- [ ] Create project → Notifications appear
- [ ] Assign team members → Assignment notifications
- [ ] Mark project complete → Completion notifications
- [ ] Notification dropdown shows count
- [ ] Mark notifications as read
- [ ] Delete notifications

### Manual Triggers
- [ ] Check Due Dates button works
- [ ] Create Test Notifications works
- [ ] View Stats shows information
- [ ] No errors in browser console
- [ ] No errors in Render logs

### Due Date Notifications
- [ ] Create project with end date 3 days from now
- [ ] Click "Check Due Dates"
- [ ] See 3-day reminder notifications
- [ ] Create project due tomorrow
- [ ] Click "Check Due Dates"
- [ ] See HIGH ALERT notifications

---

## 🔍 Troubleshooting

### No Notifications Appearing
1. **Check Render logs** for PHP errors
2. **Verify database** has notification records
3. **Test API endpoints** directly
4. **Check browser console** for JavaScript errors

### Manual Triggers Not Working
1. **Check CSRF token** in requests
2. **Verify user authentication**
3. **Check route definitions**
4. **Test with Postman/cURL**

### Event Listeners Not Firing
1. **Verify EventServiceProvider** registration
2. **Check event dispatching** in Project model
3. **Test listeners manually**

---

## 📊 Monitoring

### Check System Health
```bash
# Get notification stats
GET /notifications/stats

# Check database
curl https://your-app.com/test-db

# Check recent notifications
curl https://your-app.com/notifications/latest
```

### Monitor Logs
- Check Render dashboard for application logs
- Look for notification-related log entries
- Monitor for errors or warnings

---

## 🎯 Expected Behavior

### When Project Created
```
User creates project
  ↓
Project::create() fires
  ↓
ProjectCreated event dispatched
  ↓
SendProjectCreatedNotification listener
  ↓
Notifications created for creator + team members
  ↓
Users see notifications immediately
```

### When Manual Due Date Check
```
User clicks "Check Due Dates"
  ↓
POST /notifications/trigger/check-due-dates
  ↓
NotificationTriggerController::checkDueDates()
  ↓
NotificationService::checkProjectDueDates()
  ↓
Scans all projects for due dates
  ↓
Creates notifications based on days remaining
  ↓
Users see new notifications
```

---

## 🚀 Production Usage

### Daily Workflow
1. **Morning**: Click "Check Due Dates" to send morning reminders
2. **Evening**: Click "Check Due Dates" to send evening reminders
3. **Monitor**: Use "View Stats" to check system health
4. **Clean up**: Use "Clear Read" to remove old notifications

### Automated Setup (Recommended)
Set up an external cron service to call the due date check endpoint automatically.

---

## 📝 Notes

- **No queue workers needed** - Everything runs synchronously
- **Manual triggers replace cron** - Use the Tools dropdown
- **External cron optional** - Can use cron-job.org or similar
- **All functionality preserved** - Just different triggering mechanism
- **Better for debugging** - Can trigger manually to test

---

## ✅ Success Indicators

The system is working if:
- ✅ Project creation creates notifications
- ✅ Manual triggers work without errors
- ✅ Notifications appear in UI
- ✅ No errors in Render logs
- ✅ Database has notification records
- ✅ All notification types work

---

**The notification system is now fully compatible with Render deployment!**
