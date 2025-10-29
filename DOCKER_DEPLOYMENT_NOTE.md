# 🐳 Docker-Based Deployment for Render.com

## Why Docker?

Render.com's free tier **doesn't support native PHP runtime** in Blueprint files. Instead, we use Docker to containerize your Laravel application.

---

## What Changed from Original Setup

### ❌ Removed (Not Needed)
- `render-build.sh` (build steps now in Dockerfile)
- `Procfile` (startup command now in Dockerfile)
- Native PHP runtime configuration

### ✅ Added (Docker Files)
1. **`Dockerfile`** - Defines how to build your app container
2. **`nginx.conf`** - Web server configuration for Laravel
3. **`.dockerignore`** - Optimizes Docker build (excludes unnecessary files)

### 📝 Updated
- **`render.yaml`** - Changed from `env: php` to `runtime: docker`

---

## How It Works

### Build Process (Automated)

When you deploy, Render will:

1. **Pull your code** from GitHub
2. **Build Docker image** using `Dockerfile`:
   - Install PHP 8.2 + extensions
   - Install Nginx web server
   - Install Composer dependencies
   - Install npm dependencies
   - Build frontend assets (Vite)
   - Set proper permissions
3. **Start container** with:
   - Run migrations
   - Seed database
   - Cache configs
   - Start PHP-FPM
   - Start Nginx

### Container Structure

```
┌─────────────────────────────────┐
│      Docker Container           │
│                                 │
│  ┌──────────┐   ┌────────────┐ │
│  │  Nginx   │──▶│  PHP-FPM   │ │
│  │  :8080   │   │  Laravel   │ │
│  └──────────┘   └────────────┘ │
│                                 │
│  /var/www/html (Your App)      │
└─────────────────────────────────┘
         │
         ▼
   PostgreSQL Database
   (Separate service)
```

---

## Deployment Steps (Same as Before)

### 1. Push to GitHub

```bash
git add .
git commit -m "Add Docker deployment for Render"
git push origin main
```

### 2. Deploy on Render

1. Go to https://dashboard.render.com
2. New + → Blueprint
3. Connect repository
4. Click Apply

### 3. Wait & Access

- First build: **10-15 minutes** (Docker builds are slower)
- Subsequent builds: **5-10 minutes**
- Access your URL when "Deploy live" appears

---

## Key Differences from Shell Script Approach

| Aspect | Shell Script | Docker (Current) |
|--------|--------------|------------------|
| **Runtime** | Native PHP | Containerized PHP + Nginx |
| **Build Time** | 5-8 min | 10-15 min (first time) |
| **Reliability** | Depends on Render's PHP setup | Isolated environment |
| **Portability** | Render-specific | Works anywhere Docker runs |
| **Debugging** | Limited | Full container access |

---

## What's Inside the Dockerfile?

### Base Image
- PHP 8.2-FPM (Official)

### Installed Packages
- **Web Server**: Nginx
- **Database**: PostgreSQL extensions (pdo_pgsql, pgsql)
- **PHP Extensions**: mbstring, exif, pcntl, bcmath, gd
- **Build Tools**: Composer, Node.js, npm
- **System Tools**: Git, curl, zip, unzip

### Application Setup
1. Copy all application files
2. Install Composer dependencies (production mode)
3. Install npm dependencies
4. Build assets with Vite
5. Set storage permissions
6. Configure Nginx to serve Laravel

### Startup Sequence
```bash
1. php artisan migrate --force
2. php artisan db:seed --force
3. php artisan config:cache
4. php artisan route:cache
5. php artisan view:cache
6. php artisan storage:link
7. Start PHP-FPM (background)
8. Start Nginx (foreground)
```

---

## Benefits of Docker Approach

### ✅ Advantages

1. **Consistency**: Same environment everywhere
2. **Isolation**: Your app doesn't affect others
3. **Portability**: Can deploy to AWS, GCP, Azure, DigitalOcean, etc.
4. **Version Control**: Dockerfile is versioned with code
5. **Debugging**: Can run same container locally

### ⚠️ Trade-offs

1. **Build Time**: Slower initial builds (caching helps)
2. **Complexity**: More moving parts
3. **Image Size**: Larger than native runtime (but optimized)

---

## Local Testing (Optional)

You can test the same Docker container locally:

### Prerequisites
- Docker Desktop installed

### Build & Run Locally

```bash
# Build image
docker build -t mytime-app .

# Run container
docker run -p 8080:8080 \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=/var/www/html/database/database.sqlite \
  mytime-app

# Access at http://localhost:8080
```

---

## Troubleshooting Docker Builds

### Issue: Build Fails at npm Install

**Cause**: Memory limit on free tier

**Solution**: Already optimized in Dockerfile with `npm ci`

### Issue: Build Fails at Composer Install

**Cause**: Missing PHP extensions

**Solution**: All required extensions included in Dockerfile

### Issue: Container Starts but 500 Error

**Cause**: APP_KEY not generated

**Solution**: 
1. Go to Render dashboard → Environment
2. Verify `APP_KEY` is set
3. Redeploy if needed

### Issue: Assets Not Loading

**Cause**: Nginx misconfiguration or build failed

**Solution**:
1. Check build logs for `npm run build` success
2. Verify nginx.conf is in repository
3. Check Nginx logs in container

---

## Environment Variables (Same as Before)

All auto-configured via `render.yaml`:

```yaml
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generated>
DB_CONNECTION=pgsql
DB_HOST=<from-database>
DB_PORT=<from-database>
DB_DATABASE=<from-database>
DB_USERNAME=<from-database>
DB_PASSWORD=<from-database>
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## Performance Notes

### Cold Starts
- **Free Tier**: App sleeps after 15 min inactivity
- **Wake Time**: 30-60 seconds (container startup)
- **Warm Requests**: Fast (similar to native)

### Resource Usage
- **Memory**: ~500MB (PHP-FPM + Nginx)
- **CPU**: Minimal when idle
- **Storage**: ~200MB image size

---

## Updating Your App

### Code Changes

```bash
git add .
git commit -m "Update feature X"
git push origin main
```

Render auto-deploys (5-10 min rebuild).

### Dockerfile Changes

If you modify `Dockerfile`, `nginx.conf`, or dependencies:
1. Push changes to GitHub
2. Render rebuilds entire image
3. Takes longer (10-15 min)

---

## Advanced: Dockerfile Customization

### Add PHP Extension

```dockerfile
RUN docker-php-ext-install <extension-name>
```

### Add System Package

```dockerfile
RUN apt-get update && apt-get install -y <package-name>
```

### Optimize Image Size

```dockerfile
# Use multi-stage build
FROM composer:latest AS composer
# ... copy and install dependencies

FROM php:8.2-fpm
COPY --from=composer /app/vendor /var/www/html/vendor
```

---

## Migration from Shell Script Approach

If you previously tried the shell script approach:

### Files to Remove
- ✅ Delete `render-build.sh` (optional, won't hurt)
- ✅ Delete `Procfile` (optional, won't hurt)

### Files to Keep
- ✅ All `.md` documentation files
- ✅ `.env.render` (reference)
- ✅ Database configuration updates

### No Data Loss
- Database persists (separate service)
- Environment variables preserved
- User data safe

---

## Comparison with Other Platforms

| Platform | Approach | Free Tier | Ease |
|----------|----------|-----------|------|
| **Render** | Docker | ✅ Yes | Medium |
| **Heroku** | Buildpack | ❌ No | Easy |
| **Railway** | Docker/Nixpacks | ✅ Yes | Easy |
| **Fly.io** | Docker | ✅ Yes | Medium |
| **DigitalOcean** | App Platform | ❌ No | Easy |

Render offers good balance of free tier + Docker support.

---

## Summary

✅ **Docker approach works reliably on Render.com**  
✅ **All features work same as native PHP**  
✅ **Just slower builds (acceptable for free tier)**  
✅ **More portable if you switch platforms later**  

**Your app will work exactly the same to end users!**

---

## Quick Reference

### Deploy Commands
```bash
git add .
git commit -m "Deploy updates"
git push origin main
```

### Check Build Status
Visit: https://dashboard.render.com → Your Service → Logs

### Access Container Shell
Dashboard → Shell tab → Run commands:
```bash
php artisan tinker
php artisan migrate
tail -f storage/logs/laravel.log
```

---

**Questions?** Check `RENDER_DEPLOYMENT_GUIDE.md` for detailed guide!
