#!/bin/bash
set -e

APP_DIR="/var/www/internal_audit"

echo "====================================================="
echo "  MSU Internal Audit System — Incremental Update"
echo "====================================================="

echo "[1/8] Extracting updated application files..."
echo 'khuda..1' | sudo -S chown -R kuda:kuda $APP_DIR
unzip -o /home/kuda/internal_audit.zip -d $APP_DIR

cd $APP_DIR

echo "[2/8] Preserving production .env (no overwrite)..."
# .env on the server already has correct DB/APP settings — do not touch it

echo "[3/8] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "[4/8] Installing NPM packages and building frontend assets..."
npm ci --prefer-offline 2>/dev/null || npm install
npm run build

echo "[5/8] Running database migrations..."
php artisan migrate --force

echo "[6/8] Running database seeders (idempotent)..."
php artisan db:seed --class=UsersTableSeeder --force

echo "[7/8] Clearing and re-caching Laravel configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

echo "[8/8] Fixing file permissions..."
echo 'khuda..1' | sudo -S chown -R www-data:www-data $APP_DIR
echo 'khuda..1' | sudo -S find $APP_DIR -type d -exec chmod 755 {} \;
echo 'khuda..1' | sudo -S find $APP_DIR -type f -exec chmod 644 {} \;
echo 'khuda..1' | sudo -S chgrp -R www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
echo 'khuda..1' | sudo -S chmod -R ug+rwx $APP_DIR/storage $APP_DIR/bootstrap/cache
echo 'khuda..1' | sudo -S nginx -t && echo 'khuda..1' | sudo -S systemctl reload nginx

echo ""
echo "====================================================="
echo "  Update completed successfully!"
echo "  App URL: http://10.10.9.1:8081"
echo "====================================================="
