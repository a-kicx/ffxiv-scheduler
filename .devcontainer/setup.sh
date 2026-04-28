#!/bin/bash
set -e

# Install PHP extensions
sudo apt-get update -qq
sudo apt-get install -y -qq php8.4-sqlite3 php8.4-xml php8.4-mbstring php8.4-curl 2>/dev/null || true

# Composer install
composer install --no-interaction --prefer-dist

# .env setup
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# SQLite DB
touch database/database.sqlite

# Migrate & seed
php artisan migrate --seed --force

# Storage link
php artisan storage:link 2>/dev/null || true

echo "✅ セットアップ完了！ php artisan serve で起動できます。"
