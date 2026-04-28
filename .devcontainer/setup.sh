#!/bin/bash
set -e

# Install PHP extensions
sudo apt-get update -qq
sudo apt-get install -y -qq php8.4-sqlite3 php8.4-xml php8.4-mbstring php8.4-curl 2>/dev/null || true

# Composer install
composer install --no-interaction --prefer-dist

# Node.js 依存関係インストール & ビルド
npm install
npm run build

# .env setup
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Codespaces: APP_URL を自動設定
if [ -n "$CODESPACE_NAME" ] && [ -n "$GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN" ]; then
    APP_URL="https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
    sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env
    echo "APP_URL を Codespaces URL に設定: ${APP_URL}"
fi

# SQLite DB
touch database/database.sqlite

# Migrate & seed
php artisan migrate --seed --force

# Storage link
php artisan storage:link 2>/dev/null || true

echo "✅ セットアップ完了！ php artisan serve で起動できます。"
