#!/bin/sh
set -e

cd /var/www/html

# 無料枠のファイルは起動のたびに消えるため、毎回 DB を作り直してデモ用データを入れる
mkdir -p database
touch database/database.sqlite

php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder --force

# Apache（www-data 権限）が書き込めるようにする
chown -R www-data:www-data database storage bootstrap/cache

exec apache2-foreground
