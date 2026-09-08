#!/bin/bash
set -e

APP_DIR="/var/www/internal_audit"

echo "Creating application directory..."
echo 'khuda..1' | sudo -S mkdir -p $APP_DIR
echo 'khuda..1' | sudo -S chown -R kuda:kuda $APP_DIR

echo "Extracting application files..."
unzip -o /home/kuda/internal_audit.zip -d $APP_DIR

cd $APP_DIR

echo "Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
fi
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g' .env
sed -i 's/# DB_PORT=3306/DB_PORT=3306/g' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=internal_audit/g' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=internal_audit/g' .env
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=InternalAudit@2026/g' .env
sed -i 's/APP_ENV=local/APP_ENV=production/g' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' .env
sed -i 's|APP_URL=http://localhost:8000|APP_URL=http://10.10.9.1:8081|g' .env

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing NPM dependencies and building..."
npm install
npm run build

echo "Running Laravel deployment commands..."
php artisan key:generate
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setting file permissions..."
echo 'khuda..1' | sudo -S chown -R www-data:www-data $APP_DIR
echo 'khuda..1' | sudo -S find $APP_DIR -type d -exec chmod 755 {} \;
echo 'khuda..1' | sudo -S find $APP_DIR -type f -exec chmod 644 {} \;
echo 'khuda..1' | sudo -S chgrp -R www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
echo 'khuda..1' | sudo -S chmod -R ug+rwx $APP_DIR/storage $APP_DIR/bootstrap/cache

echo "Updating Nginx configuration..."
echo 'khuda..1' | sudo -S cp $APP_DIR/internal_audit.conf /etc/nginx/sites-available/internal_audit
echo 'khuda..1' | sudo -S ln -sf /etc/nginx/sites-available/internal_audit /etc/nginx/sites-enabled/
echo 'khuda..1' | sudo -S nginx -t && echo 'khuda..1' | sudo -S systemctl reload nginx

echo "Deployment tasks completed successfully!"
