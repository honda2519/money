#!/bin/sh
set -e

cd /var/www/html

# Recreate the DB on every start (free tier filesystem is ephemeral) and load demo data
mkdir -p database storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch database/database.sqlite

php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder --force

# Make writable for Apache (www-data)
chown -R www-data:www-data database storage bootstrap/cache

exec apache2-foreground