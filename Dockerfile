# Stage 1: Build frontend assets
FROM node:20-bullseye-slim AS frontend

WORKDIR /build

# Copy package files first for better caching
COPY package.json package-lock.json ./

# Install dependencies
RUN npm ci --legacy-peer-deps

# Copy all necessary files for Vite build
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Create minimal .env file that Vite might need
RUN echo "APP_NAME=MyTime" > .env

# Build assets
RUN npm run build

# Stage 2: Main application
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files (except node_modules and vendor which are in .dockerignore)
COPY . .

# Copy the built assets from frontend stage
COPY --from=frontend /build/public/build ./public/build

# Copy nginx configuration
COPY nginx.conf /etc/nginx/sites-available/default

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Starting application setup..."\n\
php artisan config:clear\n\
php artisan migrate --force --no-interaction || echo "Migrations failed but continuing..."\n\
php artisan db:seed --force --no-interaction || echo "Seeding failed but continuing..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan storage:link || echo "Storage link already exists"\n\
echo "Starting PHP-FPM..."\n\
php-fpm -D\n\
echo "Starting Nginx..."\n\
nginx -g "daemon off;"\n\
' > /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

# Expose port
EXPOSE 8080

# Start application
CMD ["/usr/local/bin/start.sh"]
