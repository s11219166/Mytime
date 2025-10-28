# 📧 Email Testing Guide - MyTime Application

## Current Configuration

Your email is already configured with Gmail SMTP:

```
Email: chandsalvesh7@gmail.com
SMTP: smtp.gmail.com
Port: 587
Encryption: TLS
```

---

## 🚀 Quick Test Methods

### Method 1: Using Browser (Easiest)

1. **Start your Laravel server** (if not already running):
   ```bash
   php artisan serve
   ```

2. **Log in to your application**:
   - Go to: `http://localhost:8000/login`
   - Email: `admin@mytime.com`
   - Password: `admin123`

3. **Send a simple test email**:
   - Visit: `http://localhost:8000/test-email`
   - This will send a basic test email to your logged-in user's email

4. **Send a project reminder email** (with beautiful template):
   - Visit: `http://localhost:8000/test-project-email`
   - This will send a formatted project due reminder email

5. **Check your inbox**:
   - Check: `chandsalvesh7@gmail.com`
   - Also check your **Spam/Junk folder** (Gmail might filter it initially)

---

### Method 2: Using Artisan Tinker (Command Line)

1. **Open Artisan Tinker**:
   ```bash
   php artisan tinker
   ```

2. **Send a simple test email**:
   ```php
   Mail::raw('Test email from MyTime!', function($message) {
       $message->to('chandsalvesh7@gmail.com')
               ->subject('Test Email');
   });
   ```

3. **Press Enter** and check your inbox

4. **Exit Tinker**:
   ```php
   exit
   ```

---

### Method 3: Test Project Due Reminder

1. **Open Artisan Tinker**:
   ```bash
   php artisan tinker
   ```

2. **Send project reminder email**:
   ```php
   $user = App\Models\User::first();
   $project = App\Models\Project::first();
   
   Mail::to('chandsalvesh7@gmail.com')
       ->send(new App\Mail\ProjectDueReminderMail($project, $user, 3));
   ```

3. **Check your inbox** for the beautiful HTML email

---

### Method 4: Test Automated Notifications

1. **Run the due date checker command**:
   ```bash
   php artisan projects:check-due-dates
   ```

2. This will:
   - Check all projects with due dates
   - Send reminders for projects due in 7, 3, 1 days
   - Send alerts for overdue projects
   - Create in-app notifications

3. **Check your email** for any project reminders

---

## 📋 Step-by-Step Testing Checklist

### ��� Pre-Testing Checklist:

- [x] Gmail SMTP configured in `.env`
- [x] App Password created (ybqpmvrzvpbpmcfy)
- [x] Config cache cleared
- [x] Test routes created
- [x] Laravel server running

### 🧪 Testing Steps:

1. **Test 1: Simple Email**
   ```
   Visit: http://localhost:8000/test-email
   Expected: Success message + email in inbox
   ```

2. **Test 2: Project Reminder Email**
   ```
   Visit: http://localhost:8000/test-project-email
   Expected: Beautiful HTML email with project details
   ```

3. **Test 3: Automated Notifications**
   ```
   Command: php artisan projects:check-due-dates
   Expected: Emails sent for due/overdue projects
   ```

4. **Test 4: Manual Tinker Test**
   ```
   Command: php artisan tinker
   Code: Mail::raw('Test', fn($m) => $m->to('chandsalvesh7@gmail.com')->subject('Test'));
   Expected: Email received
   ```

---

## 🔍 Troubleshooting

### Issue 1: Email Not Received

**Check Spam Folder:**
- Gmail might filter emails to spam initially
- Mark as "Not Spam" to whitelist

**Check Laravel Logs:**
```bash
# View last 50 lines of log
tail -n 50 storage/logs/laravel.log

# Or on Windows
Get-Content storage/logs/laravel.log -Tail 50
```

**Verify Configuration:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

### Issue 2: Authentication Failed

**Verify App Password:**
- Make sure you're using Google App Password, not your regular password
- App Password should be 16 characters: `ybqpmvrzvpbpmcfy`
- No spaces in the password

**Create New App Password if needed:**
1. Go to: https://myaccount.google.com/security
2. Enable 2-Step Verification (if not enabled)
3. Go to: https://myaccount.google.com/apppasswords
4. Create new app password for "Mail"
5. Copy the 16-character password
6. Update `.env` file
7. Run: `php artisan config:clear`

---

### Issue 3: Connection Timeout

**Check Firewall:**
- Ensure port 587 is not blocked
- Try port 465 with SSL instead:
  ```env
  MAIL_PORT=465
  MAIL_ENCRYPTION=ssl
  ```

**Test Connection:**
```bash
php artisan tinker
```
```php
try {
    Mail::raw('Test', fn($m) => $m->to('chandsalvesh7@gmail.com')->subject('Test'));
    echo "Email sent successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

### Issue 4: "From" Address Issues

**Gmail Requirement:**
- Gmail requires the FROM address to match your Gmail account
- Current setting: `chandsalvesh7@gmail.com` ✓ Correct

**If you want a custom FROM name:**
```env
MAIL_FROM_ADDRESS="chandsalvesh7@gmail.com"
MAIL_FROM_NAME="MyTime Project Management"
```

---

## 📊 Expected Email Examples

### Simple Test Email:
```
From: MyTime <chandsalvesh7@gmail.com>
To: chandsalvesh7@gmail.com
Subject: Test Email from MyTime

This is a test email from MyTime application. 
If you received this, your email configuration is working correctly!
```

### Project Reminder Email:
```
From: MyTime <chandsalvesh7@gmail.com>
To: chandsalvesh7@gmail.com
Subject: Project Due Reminder: [Project Name]

[Beautiful HTML email with:]
- Project details
- Progress bar
- Due date information
- Color-coded alerts
- Direct link to project
```

---

## 🎯 Quick Commands Reference

```bash
# Clear config cache
php artisan config:clear

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Start Laravel server
php artisan serve

# Open Tinker
php artisan tinker

# Check project due dates
php artisan projects:check-due-dates

# View logs (Windows PowerShell)
Get-Content storage/logs/laravel.log -Tail 50

# View logs (Git Bash/Linux)
tail -f storage/logs/laravel.log
```

---

## 🌐 Test URLs

After logging in, visit these URLs:

1. **Simple Test Email:**
   ```
   http://localhost:8000/test-email
   ```

2. **Project Reminder Email:**
   ```
   http://localhost:8000/test-project-email
   ```

3. **Create Sample Notifications:**
   ```
   http://localhost:8000/test-notifications
   ```

4. **View Notifications:**
   ```
   http://localhost:8000/notifications
   ```

---

## 📝 Notes

1. **First Email Might Go to Spam:**
   - This is normal for new sending addresses
   - Mark as "Not Spam" to whitelist

2. **Gmail Sending Limits:**
   - Free Gmail: 500 emails/day
   - More than enough for testing

3. **Production Recommendations:**
   - Use a dedicated email service (SendGrid, Mailgun, AWS SES)
   - Set up proper SPF/DKIM records
   - Use a custom domain

4. **Security:**
   - Never commit `.env` file to version control
   - Keep your App Password secure
   - Rotate passwords regularly

---

## ✅ Success Indicators

You'll know it's working when:

1. ✓ No errors in browser after visiting test URLs
2. ✓ Success message appears: "Test email sent successfully..."
3. ✓ Email appears in inbox (or spam folder)
4. ✓ Email has correct FROM address
5. ✓ HTML formatting looks good (for project emails)
6. ✓ Links in email work correctly

---

## 🆘 Still Having Issues?

1. **Check Laravel Log:**
   ```bash
   Get-Content storage/logs/laravel.log -Tail 100
   ```

2. **Test Gmail Credentials:**
   - Try logging into Gmail with your credentials
   - Verify App Password is correct

3. **Test SMTP Connection:**
   ```bash
   php artisan tinker
   ```
   ```php
   config('mail.host')  // Should show: smtp.gmail.com
   config('mail.port')  // Should show: 587
   config('mail.username')  // Should show: chandsalvesh7@gmail.com
   ```

4. **Verify .env is loaded:**
   ```bash
   php artisan config:clear
   php artisan serve
   ```

---

## 🎉 Next Steps After Successful Test

1. **Enable Notifications in Profile:**
   - Go to Profile → Preferences
   - Enable "Email Notifications"
   - Enable "Project Updates"

2. **Create Test Projects:**
   - Create projects with due dates
   - Set dates to tomorrow or next week

3. **Test Automated Reminders:**
   - Run: `php artisan projects:check-due-dates`
   - Check for reminder emails

4. **Schedule Automated Checks:**
   - Add to `app/Console/Kernel.php`
   - Set up cron job or Task Scheduler

---

## 📞 Support

If you continue to have issues:
1. Share the error from `storage/logs/laravel.log`
2. Verify your Gmail App Password is correct
3. Check if 2-Step Verification is enabled on your Google account
4. Try creating a new App Password

Good luck with your testing! 🚀
