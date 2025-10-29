#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing npm dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

echo "Running database migrations..."
php artisan migrate --force --no-interaction

echo "Seeding database..."
php artisan db:seed --force --no-interaction

echo "Clearing and caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage link..."
php artisan storage:link

echo "Build completed successfully!"
