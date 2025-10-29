# 🚀 Quick Deploy to Render.com - 5 Minutes

## Step 1: Push to GitHub (2 min)

```bash
git add .
git commit -m "Prepare for Render deployment"
git push origin main
```

## Step 2: Make Build Script Executable

```bash
git update-index --chmod=+x render-build.sh
git commit -m "Make build script executable"
git push
```

## Step 3: Create on Render (2 min)

1. Go to https://dashboard.render.com
2. Click **"New +"** → **"Blueprint"**
3. Connect your GitHub repository
4. Click **"Apply"** (Render detects `render.yaml` automatically)

That's it! Wait 10-15 minutes for first deployment.

## Step 4: Login

Visit your app URL (shown in Render dashboard):

```
Email: admin@example.com
Password: password123
```

⚠️ **Change password immediately!**

---

## What Changed for Deployment?

### Database
- **Before:** SQLite (file-based)
- **After:** PostgreSQL (Render provides free)
- **Auto-configured:** Yes, via `render.yaml`

### Environment Variables
All auto-configured in `render.yaml`:
- ✅ APP_KEY (generated)
- ✅ Database credentials (linked)
- ✅ Session & Cache (using database)
- ✅ Production settings

### Build Process
Automatic via `render-build.sh`:
1. Install dependencies
2. Build assets
3. Run migrations
4. Seed database
5. Cache configs

---

## Optional: Configure Email

For notifications and password resets:

### Using Gmail:

1. Generate App Password: https://myaccount.google.com/apppasswords
2. Add to Render Environment Variables:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
```

3. Click **"Save Changes"** (app will redeploy)

---

## Troubleshooting

### Build Failed?
Check logs in Render dashboard → Look for specific error

### Can't Access App?
Wait for "Deploy live" message in logs (10-15 min first time)

### 500 Error?
1. Check APP_KEY is set
2. View logs for details

### App Sleeping?
Free tier sleeps after 15 min inactivity. First request wakes it (30-60 sec).

---

## Future Updates

Just push to GitHub - Render auto-deploys:

```bash
git add .
git commit -m "Your update"
git push
```

---

For detailed guide, see **RENDER_DEPLOYMENT_GUIDE.md**
