# 🔔 Automated Hourly Notifications - Setup Guide

## ✅ What's Now Automated

Your Laravel app now automatically checks **EVERY HOUR** for:
1. **Project due dates** (1, 2, 3 days before, due today, overdue)
2. **New projects** added
3. Sends **both in-app notifications AND emails**

---

## 🎯 How It Works

### **Hourly Checks (Every Hour, 24/7)**
- Runs Laravel scheduler every hour
- Checks all projects with due dates
- Sends notifications based on urgency
- Sends emails to users with email notifications enabled

### **Special Daily Checks**
- **9:00 AM**: Comprehensive morning check
- **6:00 PM**: Evening reminder check

---

## ⚙️ Email Configuration Required

To enable email sending, add these environment variables in **Render Dashboard**:

### **Option 1: Gmail (Recommended for Testing)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=MyTime
```

**To get Gmail App Password:**
1. Go to: https://myaccount.google.com/apppasswords
2. Enable 2-Factor Authentication if not already enabled
3. Click "App passwords"
4. Select "Mail" and "Other (Custom name)"
5. Name it "MyTime Laravel"
6. Copy the 16-character password
7. Use this password in `MAIL_PASSWORD`

### **Option 2: Mailtrap (For Testing)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mytime.com
MAIL_FROM_NAME=MyTime
```

### **Option 3: SendGrid (For Production)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=MyTime
```

---

## 📋 Notification Schedule

| Time Until Due | Frequency | Type | Email Sent |
|----------------|-----------|------|------------|
| **3 days** | 2x daily (9 AM & 6 PM) | 📅 Reminder | ✅ Yes |
| **2 days** | 1x daily (9 AM) | ⚠️ Moderate Alert | ✅ Yes |
| **1 day** | 1x daily (9 AM) | 🚨 High Alert | ✅ Yes |
| **Due today** | 1x daily (9 AM) | 🔴 Critical | ✅ Yes |
| **Overdue** | 1x daily (9 AM) | ❌ Overdue | ✅ Yes |

---

## 🎯 Who Receives Notifications

### **In-App Notifications:**
- ✅ Project creator (always)
- ✅ Team members (if they have `project_updates` enabled)

### **Email Notifications:**
- ✅ Users with `email_notifications` flag set to true
- ✅ Skipped if email is not configured

---

## 🚀 Deployment Steps

### **1. Add Environment Variables**

In Render Dashboard → `mytime-app` → **Environment**:

Add the mail configuration variables above (choose Gmail, Mailtrap, or SendGrid)

### **2. Code is Already Pushed**

The following files have been updated:
- ✅ `routes/console.php` - Hourly scheduler
- ✅ `Dockerfile` - Cron installed and configured
- ✅ `app/Services/NotificationService.php` - Already sends emails

### **3. Deploy**

Render will auto-deploy (~3 minutes)

### **4. Verify**

After deployment:
1. Check Render logs for "Starting Laravel Scheduler (cron)..."
2. Wait for the next hour
3. Check notifications in your app
4. Check email inbox

---

## 📧 Email Template

Users will receive emails like this:

```
Subject: [MyTime] Project Due Soon: Your Project Name

Hello User,

This is a reminder about your project "Your Project Name".

⚠️ Days Remaining: 3 days
📅 Due Date: December 15, 2025
🎯 Status: Active
📊 Progress: 45%

[View Project Details]

Please ensure all tasks are completed before the deadline.

Best regards,
MyTime Team
```

---

## 🔍 Testing Notifications

### **Test Immediately (Don't Wait for Hourly)**

Run manually in Render Shell:

```bash
php artisan projects:check-due-dates
```

Or test locally:

```bash
cd c:\Users\salve\Downloads\Mytime
php artisan projects:check-due-dates
```

### **What to Check:**
1. **In-App**: Go to Notifications page (bell icon)
2. **Email**: Check your inbox for project reminder emails
3. **Logs**: Check `storage/logs/laravel.log` for notification records

---

## 🛠️ Troubleshooting

### **Emails Not Sending?**

1. **Check environment variables** in Render
2. **Verify Gmail app password** is correct
3. **Check logs** in Render for errors
4. **Test email config**:
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($msg) {
       $msg->to('your-email@gmail.com')->subject('Test');
   });
   ```

### **Notifications Not Appearing?**

1. **Check if cron is running**:
   ```bash
   service cron status
   ```
2. **Check scheduler log**:
   ```bash
   php artisan schedule:list
   ```
3. **Run manually to test**:
   ```bash
   php artisan projects:check-due-dates
   ```

### **Hourly Check Not Running?**

1. **Verify crontab**:
   ```bash
   crontab -l
   ```
   Should show: `* * * * * cd /var/www/html && php artisan schedule:run`

2. **Check if projects have due dates**:
   - Projects need `end_date` set
   - Projects must not be `completed` or `cancelled`

---

## 📊 What Gets Logged

Every hour, you'll see logs like:

```
[2025-10-30 14:00:00] Checking 15 projects for due date notifications
[2025-10-30 14:00:00] Project 'Website Redesign': 3 days remaining - morning notification
[2025-10-30 14:00:00] Project 'Mobile App': 1 day remaining - HIGH ALERT
[2025-10-30 14:00:00] Sent notification to user@example.com
[2025-10-30 14:00:00] Sent email to user@example.com
```

---

## ✅ Summary

| Feature | Status |
|---------|--------|
| Hourly scheduler | ✅ Configured |
| Cron installed | ✅ In Dockerfile |
| Email sending | ⏳ Needs mail config |
| In-app notifications | ✅ Working |
| Due date checks | ✅ Automated |
| New project alerts | ✅ Automated |

---

## 🎉 Final Steps

1. **Add mail environment variables** in Render (see above)
2. **Wait for deployment** (~3 min)
3. **Check notifications** after the next hour
4. **Verify emails** are being sent

**That's it! Your notifications are now fully automated!** 🚀✉️🔔
