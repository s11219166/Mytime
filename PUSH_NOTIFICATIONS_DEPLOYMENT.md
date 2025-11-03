# Push Notifications - Deployment Guide

## Pre-Deployment Checklist

- [ ] All code committed to git
- [ ] Database backup created
- [ ] Tests passing
- [ ] Documentation reviewed
- [ ] Team notified

## Development Environment Setup

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Run Migration
```bash
php artisan migrate
```

### 3. Start Development Server
```bash
php artisan serve
```

### 4. Start Scheduler (in new terminal)
```bash
php artisan schedule:work
```

### 5. Verify Installation
```bash
# Check service files
ls -la app/Services/PushNotificationService.php
ls -la app/Http/Controllers/PushNotificationController.php
ls -la public/service-worker.js
ls -la public/js/push-notifications.js

# Check routes
php artisan route:list | grep push-notifications

# Check scheduler
php artisan schedule:list
```

## Production Environment Setup

### 1. SSH into Server
```bash
ssh user@your-server.com
cd /path/to/mytime
```

### 2. Pull Latest Code
```bash
git pull origin main
```

### 3. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

### 4. Run Migrations
```bash
php artisan migrate --force
```

### 5. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 6. Set Up Cron Job

#### Option A: Using Crontab
```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path/to/mytime && php artisan schedule:run >> /dev/null 2>&1
```

#### Option B: Using Supervisor (Recommended)
Create `/etc/supervisor/conf.d/mytime-scheduler.conf`:
```ini
[program:mytime-scheduler]
process_name=%(program_name)s
command=php /path/to/mytime/artisan schedule:work
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/mytime-scheduler.log
user=www-data
```

Then run:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mytime-scheduler
```

### 7. Verify Scheduler
```bash
# Check if running
sudo supervisorctl status mytime-scheduler

# Or check cron
sudo tail -f /var/log/syslog | grep CRON
```

### 8. Monitor Logs
```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log

# Watch scheduler logs
tail -f /var/log/mytime-scheduler.log
```

## Docker Deployment

### Dockerfile Configuration
```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
```

### Docker Compose Configuration
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: mytime-app
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
    networks:
      - mytime

  scheduler:
    build: .
    container_name: mytime-scheduler
    working_dir: /var/www/html
    command: php artisan schedule:work
    volumes:
      - ./:/var/www/html
    networks:
      - mytime
    depends_on:
      - app

  nginx:
    image: nginx:alpine
    container_name: mytime-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./nginx.conf:/etc/nginx/nginx.conf
    networks:
      - mytime
    depends_on:
      - app

  db:
    image: mysql:8.0
    container_name: mytime-db
    environment:
      MYSQL_DATABASE: mytime
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - mytime

networks:
  mytime:
    driver: bridge

volumes:
  dbdata:
```

Deploy with:
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
```

## Render.com Deployment

### 1. Create render.yaml
```yaml
services:
  - type: web
    name: mytime
    env: php
    plan: free
    buildCommand: composer install && npm install && npm run build && php artisan migrate --force
    startCommand: php artisan serve --host 0.0.0.0 --port 10000
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: DATABASE_URL
        fromDatabase:
          name: mytime-db
          property: connectionString

  - type: cron
    name: mytime-scheduler
    schedule: "* * * * *"
    command: php artisan schedule:run

  - type: pserv
    name: mytime-db
    env: mysql
    plan: free
    ipAllowList: []
```

### 2. Deploy
```bash
git push origin main
# Render will automatically deploy
```

## AWS Deployment

### 1. Create EC2 Instance
```bash
# Launch Ubuntu 22.04 LTS instance
# Security group: Allow HTTP, HTTPS, SSH
```

### 2. Install Dependencies
```bash
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-gd nginx mysql-client git curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-repo/mytime.git
cd mytime
sudo chown -R www-data:www-data .
```

### 4. Install Application
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install --production
sudo -u www-data npm run build
sudo -u www-data php artisan migrate --force
```

### 5. Configure Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/mytime/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. Set Up SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 7. Set Up Cron Job
```bash
sudo crontab -e

# Add:
* * * * * cd /var/www/mytime && php artisan schedule:run >> /dev/null 2>&1
```

## Heroku Deployment

### 1. Create Procfile
```
web: vendor/bin/heroku-php-apache2 public/
scheduler: php artisan schedule:work
```

### 2. Create app.json
```json
{
  "name": "MyTime",
  "description": "Project Management & Time Tracking",
  "buildpacks": [
    {
      "url": "heroku/php"
    },
    {
      "url": "heroku/nodejs"
    }
  ],
  "env": {
    "APP_ENV": {
      "value": "production"
    },
    "APP_DEBUG": {
      "value": "false"
    }
  }
}
```

### 3. Deploy
```bash
heroku create your-app-name
git push heroku main
heroku run php artisan migrate
```

## Post-Deployment Verification

### 1. Check Application
```bash
# Visit your domain
curl https://your-domain.com

# Check status code (should be 200)
curl -I https://your-domain.com
```

### 2. Check Scheduler
```bash
# Verify cron job
sudo tail -f /var/log/syslog | grep CRON

# Or check supervisor
sudo supervisorctl status mytime-scheduler
```

### 3. Check Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
```

### 4. Test Notifications
```bash
# SSH into server
ssh user@your-server.com

# Run artisan tinker
php artisan tinker

# Send test notification
>>> $user = User::first();
>>> app(PushNotificationService::class)->sendTestNotification($user);
```

## Monitoring & Maintenance

### Daily Checks
```bash
# Check scheduler is running
ps aux | grep schedule:work

# Check for errors
grep -i error storage/logs/laravel.log

# Check disk space
df -h
```

### Weekly Checks
```bash
# Check database size
mysql -u root -p -e "SELECT table_schema, ROUND(SUM(data_length+index_length)/1024/1024,2) FROM information_schema.tables GROUP BY table_schema;"

# Check log file size
du -sh storage/logs/

# Backup database
mysqldump -u root -p mytime > backup-$(date +%Y%m%d).sql
```

### Monthly Maintenance
```bash
# Clear old logs
find storage/logs -name "*.log" -mtime +30 -delete

# Optimize database
php artisan tinker
>>> DB::statement('OPTIMIZE TABLE notifications');
>>> DB::statement('OPTIMIZE TABLE users');

# Update dependencies
composer update
npm update
```

## Troubleshooting Deployment

### Issue: Scheduler Not Running
```bash
# Check if cron job exists
crontab -l

# Check if supervisor is running
sudo supervisorctl status

# Check logs
sudo tail -f /var/log/syslog
```

### Issue: Notifications Not Sending
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check service worker
curl https://your-domain.com/service-worker.js

# Check logs
tail -f storage/logs/laravel.log | grep -i push
```

### Issue: High CPU Usage
```bash
# Check running processes
ps aux | grep php

# Check if multiple schedulers running
ps aux | grep schedule:work

# Kill duplicate processes
pkill -f "schedule:work"
```

## Rollback Procedure

If something goes wrong:

```bash
# 1. Revert code
git revert HEAD

# 2. Revert database (if needed)
php artisan migrate:rollback

# 3. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart mytime-scheduler

# 4. Verify
curl https://your-domain.com
```

## Performance Optimization

### 1. Enable Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Optimize Autoloader
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Enable OPcache
Add to php.ini:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### 4. Use Redis for Cache
```bash
# Install Redis
sudo apt install redis-server

# Update .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Security Hardening

### 1. Update Permissions
```bash
sudo chown -R www-data:www-data /var/www/mytime
sudo chmod -R 755 /var/www/mytime
sudo chmod -R 775 /var/www/mytime/storage
sudo chmod -R 775 /var/www/mytime/bootstrap/cache
```

### 2. Enable HTTPS
```bash
# Already done with Let's Encrypt above
# Verify
curl -I https://your-domain.com
```

### 3. Set Security Headers
Already configured in nginx.conf above

### 4. Regular Updates
```bash
sudo apt update
sudo apt upgrade
composer update
npm update
```

## Backup Strategy

### Automated Backups
```bash
# Create backup script
cat > /usr/local/bin/backup-mytime.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backups/mytime"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u root -p$DB_PASSWORD mytime > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/mytime

# Keep only last 30 days
find $BACKUP_DIR -mtime +30 -delete
EOF

chmod +x /usr/local/bin/backup-mytime.sh

# Add to crontab
0 2 * * * /usr/local/bin/backup-mytime.sh
```

## Success Checklist

- [ ] Application deployed successfully
- [ ] Database migrated
- [ ] Scheduler running
- [ ] Notifications working
- [ ] SSL certificate installed
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Team notified
- [ ] Documentation updated

---

**Deployment Complete!** 🎉

Your push notification system is now live and monitoring projects 24/7.
