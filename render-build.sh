#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing npm dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Generating APP_KEY if not set..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

echo "Running database migrations..."
php artisan migrate --force --no-interaction

echo "Seeding database..."
php artisan db:seed --force --no-interaction

echo "Clearing cache before caching config..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "Caching config and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link

echo "Build completed successfully!"
