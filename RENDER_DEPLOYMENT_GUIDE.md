# 🚀 Render.com Deployment Guide for MyTime App

This guide will walk you through deploying your Laravel application on Render.com's free tier.

## 📋 Prerequisites

- GitHub account with your code pushed
- Render.com account (sign up at https://render.com)
- All code committed and pushed to GitHub

---

## 🗄️ Step 1: Database Changes Required

### 1.1 Update Database Configuration

Since Render's free tier doesn't support SQLite persistence, we'll use PostgreSQL (provided free by Render).

**Files already created for you:**
- ✅ `render.yaml` - Render configuration
- ✅ `render-build.sh` - Build script
- ✅ `.env.render` - Environment template

### 1.2 Ensure PostgreSQL Support in composer.json

No changes needed - Laravel 12 includes PostgreSQL support by default.

---

## 🔧 Step 2: Prepare Your GitHub Repository

### 2.1 Add files to git:

```bash
git add render.yaml render-build.sh .env.render RENDER_DEPLOYMENT_GUIDE.md
git commit -m "Add Render.com deployment configuration"
git push origin main
```

### 2.2 Make build script executable:

```bash
git update-index --chmod=+x render-build.sh
git commit -m "Make render-build.sh executable"
git push origin main
```

---

## 🎯 Step 3: Deploy on Render.com

### 3.1 Create New Project from GitHub

1. Go to https://dashboard.render.com/
2. Click **"New +"** button
3. Select **"Blueprint"**
4. Click **"Connect a repository"**
5. Authorize GitHub and select your `Mytime` repository
6. Render will detect the `render.yaml` file automatically

### 3.2 Review Configuration

Render will show you:
- ✅ Web Service: `mytime-app` (PHP)
- ✅ PostgreSQL Database: `mytime-db` (Free tier)

Click **"Apply"** to start deployment.

---

## 🔑 Step 4: Configure Environment Variables

After deployment starts, you need to add/verify these environment variables:

### 4.1 Navigate to Your Web Service

1. Go to your Render Dashboard
2. Click on **"mytime-app"** service
3. Go to **"Environment"** tab

### 4.2 Required Environment Variables

Most are auto-configured via `render.yaml`, but verify these:

| Variable | Value | Notes |
|----------|-------|-------|
| `APP_NAME` | MyTime | Your app name |
| `APP_ENV` | production | Already set |
| `APP_DEBUG` | false | Already set |
| `APP_KEY` | (auto-generated) | Render generates this |
| `APP_URL` | https://your-app.onrender.com | Update after getting URL |
| `DB_*` | (auto-filled) | Connected from database |

### 4.3 Generate APP_KEY (if needed)

If `APP_KEY` isn't auto-generated:

1. In Render dashboard, go to "Shell" tab
2. Run: `php artisan key:generate --show`
3. Copy the output
4. Add as environment variable with format: `base64:xxxxx`

---

## 📧 Step 5: Configure Email (Optional but Recommended)

For notifications and password resets:

### Option A: Gmail (Easiest)

1. Go to Google Account → Security
2. Enable 2-Factor Authentication
3. Generate App Password: https://myaccount.google.com/apppasswords
4. Add to Render environment variables:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=MyTime
```

### Option B: Other Providers

- **SendGrid**: Free 100 emails/day
- **Mailgun**: Free 5,000 emails/month
- **Mailtrap**: For testing only

---

## 🏗️ Step 6: Initial Deployment & Database Setup

### 6.1 Monitor Deployment

1. Go to "Logs" tab in Render dashboard
2. Watch for:
   - ✅ Dependencies installed
   - ✅ Assets built
   - ✅ Migrations run
   - ✅ Database seeded
   - ✅ "Deploy live" message

### 6.2 Deployment Timeline

- First deployment: **10-15 minutes** (free tier)
- Subsequent deployments: **5-10 minutes**

### 6.3 Verify Database

The build script automatically:
- Runs all migrations
- Seeds the admin user
- Seeds initial data

---

## 🎉 Step 7: Access Your Application

### 7.1 Get Your URL

1. In Render dashboard, find your app URL
2. Format: `https://mytime-app-xxxx.onrender.com`
3. Click the URL or visit in browser

### 7.2 Login Credentials

**Admin Account:**
```
Email: admin@example.com
Password: password123
```

**⚠️ IMPORTANT:** Change admin password immediately after first login!

---

## 🔍 Step 8: Troubleshooting

### Issue 1: White Screen / 500 Error

**Solution:**
1. Check Render logs
2. Verify `APP_KEY` is set
3. Run in Shell: `php artisan config:cache`

### Issue 2: Database Connection Failed

**Solution:**
1. Verify database is running (check Database tab)
2. Confirm DB_* variables are linked to database
3. Check database connection in Shell: `php artisan tinker` → `DB::connection()->getPdo()`

### Issue 3: Build Failed

**Solution:**
1. Check if `render-build.sh` has execute permissions
2. Verify all dependencies in `composer.json` are compatible
3. Check build logs for specific error

### Issue 4: Assets Not Loading

**Solution:**
1. Check if `npm run build` succeeded
2. Verify `ASSET_URL` if using CDN
3. Run: `php artisan storage:link`

### Issue 5: Render Free Tier Sleeping

**Info:**
- Free tier apps sleep after 15 minutes of inactivity
- First request after sleep takes 30-60 seconds to wake up
- This is normal for free tier

**Solutions:**
- Upgrade to paid tier (prevents sleeping)
- Use a uptime monitor (like UptimeRobot) to ping every 14 minutes
- Accept the wake-up delay for free hosting

---

## 🔄 Step 9: Update Your App (Future Changes)

### 9.1 Deploy Updates

Simply push to GitHub:

```bash
git add .
git commit -m "Your changes"
git push origin main
```

Render auto-deploys on every push to `main` branch.

### 9.2 Manual Deploy

In Render dashboard:
1. Go to your service
2. Click "Manual Deploy" → "Deploy latest commit"

### 9.3 Run Migrations After Update

Migrations run automatically during deploy via `render-build.sh`.

To run manually:
1. Go to Shell tab
2. Run: `php artisan migrate --force`

---

## 📊 Step 10: Monitor Your Application

### 10.1 Render Dashboard Features

- **Metrics**: View CPU, Memory, Request stats
- **Logs**: Real-time application logs
- **Shell**: SSH-like access to your app
- **Events**: Deployment history

### 10.2 Application Health Checks

Render automatically monitors:
- HTTP response codes
- Response times
- Uptime status

---

## ⚙️ Step 11: Advanced Configuration

### 11.1 Custom Domain (Optional)

1. Go to Settings tab
2. Click "Custom Domain"
3. Add your domain
4. Update DNS records as shown
5. Update `APP_URL` environment variable

### 11.2 Scheduled Tasks (Cron Jobs)

For your `CheckProjectDueDates` command:

1. Create new **Cron Job** in Render
2. Command: `php artisan schedule:run`
3. Schedule: `* * * * *` (every minute)
4. Link to same database

### 11.3 Background Jobs (Queue Worker)

Create a new **Background Worker**:

1. New → Background Worker
2. Command: `php artisan queue:work --tries=3 --timeout=60`
3. Link to same database and environment

---

## 🔒 Step 12: Security Best Practices

### 12.1 Immediately After Deployment

- [ ] Change admin password
- [ ] Set `APP_DEBUG=false`
- [ ] Verify `APP_ENV=production`
- [ ] Review all user accounts

### 12.2 Ongoing Security

- [ ] Keep Laravel updated
- [ ] Monitor Render security advisories
- [ ] Use strong passwords
- [ ] Enable 2FA where possible
- [ ] Regular database backups (manual on free tier)

---

## 💾 Step 13: Database Backups

### Free Tier Limitations

- No automatic backups on free tier
- Database persists but not backed up

### Manual Backup Options

**Option 1: Via Shell**
```bash
pg_dump -h $DB_HOST -U $DB_USERNAME -d $DB_DATABASE > backup.sql
```

**Option 2: Use Database Management Tool**
- Connect to your PostgreSQL using credentials from Render
- Use tools like pgAdmin, DBeaver, or TablePlus

---

## 📈 Step 14: Upgrade Path (When Ready)

### When to Upgrade from Free Tier

Consider upgrading when:
- Need faster performance
- Want to prevent sleeping
- Need automatic backups
- Require more compute resources

### Render Pricing

- **Starter**: $7/month (no sleeping, better performance)
- **Pro**: $25/month (scaling, better resources)
- **Database**: $7/month (backups included)

---

## 🆘 Getting Help

### Resources

- **Render Docs**: https://render.com/docs
- **Laravel Docs**: https://laravel.com/docs
- **This Project Issues**: Check your GitHub repo

### Common Issues & Solutions

See Step 8: Troubleshooting above.

---

## ✅ Deployment Checklist

Before going live:

- [ ] Code pushed to GitHub
- [ ] `render.yaml` configured
- [ ] `render-build.sh` executable
- [ ] Render project created
- [ ] Environment variables set
- [ ] Database connected
- [ ] App deployed successfully
- [ ] Can access app URL
- [ ] Can login as admin
- [ ] Admin password changed
- [ ] Email configured (optional)
- [ ] Test core features
- [ ] Monitor logs for errors

---

## 🎊 Success!

Your MyTime application is now live on Render.com! 

**Next Steps:**
1. Share your app URL
2. Create user accounts
3. Start managing projects and time
4. Set up scheduled tasks for notifications
5. Configure email for full functionality

---

## 📝 Quick Reference Commands

### Run in Render Shell

```bash
# Check app status
php artisan about

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Create new admin user
php artisan tinker
>>> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);

# Check database connection
php artisan db:show

# View logs
tail -f storage/logs/laravel.log
```

---

**Questions?** Check the troubleshooting section or Render documentation!

Good luck with your deployment! 🚀
