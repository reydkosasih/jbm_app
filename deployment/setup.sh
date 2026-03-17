#!/bin/bash
set -e

APP_DIR=/var/www/jbm_app
ENV_FILE=.env
DB_NAME=jbm_bengkel

sudo apt update
sudo apt install -y nginx mysql-server php php-fpm php-mysql php-xml php-mbstring php-curl php-zip unzip curl git composer

if [ ! -d "$APP_DIR" ]; then
    sudo mkdir -p "$APP_DIR"
    sudo chown -R "$USER":"$USER" "$APP_DIR"
fi

cd "$APP_DIR"
composer install --no-dev --optimize-autoloader

if [ ! -f "$ENV_FILE" ] && [ -f .env.example ]; then
    cp .env.example "$ENV_FILE"
fi

if [ -f database/jbm_schema.sql ]; then
    mysql -u root "$DB_NAME" < database/jbm_schema.sql || true
fi

mkdir -p uploads/gallery uploads/avatars uploads/payment_proofs uploads/services application/logs
chmod -R 755 uploads application/logs

echo "Setup complete. Update .env values and web server config before going live."
