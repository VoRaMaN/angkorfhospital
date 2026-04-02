#!/bin/bash

# ============================================================================
# ANGKOR HOSPITAL - LARAVEL VPS DEPLOYMENT SCRIPT
# Ubuntu 24.04 LTS | PHP 8.3 | Nginx | MySQL
# ============================================================================

set -e  # Exit on any error

echo "=========================================="
echo "ANGKOR HOSPITAL - DEPLOYMENT STARTING"
echo "=========================================="
echo ""

# ============================================================================
# STEP 1: SYSTEM UPDATE & PACKAGE INSTALLATION
# ============================================================================
echo "[1/9] Updating system packages..."
sudo apt update && sudo apt upgrade -y

echo "[1/9] Installing system dependencies..."
sudo apt install -y curl git wget unzip htop

# ============================================================================
# STEP 2: PHP 8.3 INSTALLATION
# ============================================================================
echo "[2/9] Installing PHP 8.3 with required extensions..."
sudo apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-xml php8.3-curl \
    php8.3-gd php8.3-mbstring php8.3-bcmath php8.3-intl php8.3-zip

echo "[2/9] Verifying PHP installation..."
php -v

# ============================================================================
# STEP 3: NGINX INSTALLATION
# ============================================================================
echo "[3/9] Installing Nginx..."
sudo apt install -y nginx

echo "[3/9] Starting Nginx..."
sudo systemctl start nginx
sudo systemctl enable nginx

# ============================================================================
# STEP 4: MYSQL INSTALLATION
# ============================================================================
echo "[4/9] Installing MySQL Server..."
sudo apt install -y mysql-server

echo "[4/9] Starting MySQL..."
sudo systemctl start mysql
sudo systemctl enable mysql

# ============================================================================
# STEP 5: CREATE DATABASE & USER
# ============================================================================
echo "[5/9] Creating MySQL database and user..."

# Generate secure password
DB_PASSWD=$(openssl rand -base64 12)

# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS angkorfhospital;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'clinic_user'@'localhost' IDENTIFIED BY '$DB_PASSWD';"
sudo mysql -e "GRANT ALL PRIVILEGES ON angkorfhospital.* TO 'clinic_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo ""
echo "⚠️  DATABASE CREDENTIALS (save these!):"
echo "   Database: angkorfhospital"
echo "   User: clinic_user"
echo "   Password: $DB_PASSWD"
echo ""

# ============================================================================
# STEP 6: CLONE REPOSITORY
# ============================================================================
echo "[6/9] Cloning repository from GitHub..."
cd /var/www

if [ ! -d "angkorfhospital" ]; then
    sudo git clone https://github.com/VoRaMaN/angkorfhospital.git
    sudo chown -R $USER:$USER /var/www/angkorfhospital
else
    echo "   Repository already exists. Updating..."
    cd /var/www/angkorfhospital
    sudo git pull
fi

cd /var/www/angkorfhospital

# ============================================================================
# STEP 7: INSTALL COMPOSER DEPENDENCIES
# ============================================================================
echo "[7/9] Installing Composer dependencies..."

if ! command -v composer &> /dev/null; then
    echo "   Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

composer install --optimize-autoloader --no-dev

# ============================================================================
# STEP 8: LARAVEL CONFIGURATION
# ============================================================================
echo "[8/9] Configuring Laravel environment..."

# Copy .env file
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate APP_KEY
php artisan key:generate --force

# Update .env with database credentials
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=angkorfhospital/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=clinic_user/' .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWD|" .env

# Update APP_URL
sed -i 's|APP_URL=.*|APP_URL=https://angkorfhospital.com|' .env

# Update APP_ENV and DEBUG
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env

# Update mail configuration
sed -i 's/MAIL_MAILER=.*/MAIL_MAILER=smtp/' .env
sed -i 's/MAIL_HOST=.*/MAIL_HOST=smtp.gmail.com/' .env
sed -i 's/MAIL_PORT=.*/MAIL_PORT=587/' .env
sed -i 's/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS="voramanvuth@gmail.com"/' .env
sed -i 's/MAIL_FROM_NAME=.*/MAIL_FROM_NAME="Angkor Hospital"/' .env

# Note: You'll need to manually set MAIL_USERNAME and MAIL_PASSWORD after deployment
echo ""
echo "⚠️  EMAIL CONFIGURATION:"
echo "   Add these to your .env manually (for Gmail SMTP):"
echo "   MAIL_USERNAME=voramanvuth@gmail.com"
echo "   MAIL_PASSWORD=<your-gmail-app-password>"
echo "   (Use an App Password if 2FA is enabled)"
echo ""

# Set proper permissions
sudo chown -R www-data:www-data /var/www/angkorfhospital
sudo chmod -R 755 /var/www/angkorfhospital
sudo chmod -R 775 /var/www/angkorfhospital/storage
sudo chmod -R 775 /var/www/angkorfhospital/bootstrap/cache

echo "[8/9] Running migrations..."
php artisan migrate --force

echo "[8/9] Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ============================================================================
# STEP 9: NGINX CONFIGURATION
# ============================================================================
echo "[9/9] Configuring Nginx..."

# Create Nginx config file
sudo tee /etc/nginx/sites-available/angkorfhospital.com > /dev/null <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name angkorfhospital.com www.angkorfhospital.com;
    root /var/www/angkorfhospital/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/angkorfhospital.com /etc/nginx/sites-enabled/

# Disable default site if enabled
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
echo "[9/9] Testing Nginx configuration..."
sudo nginx -t

# Reload Nginx
echo "[9/9] Reloading Nginx..."
sudo systemctl reload nginx

# ============================================================================
# SSL CERTIFICATE SETUP
# ============================================================================
echo ""
echo "=========================================="
echo "INSTALLING SSL CERTIFICATE (Let's Encrypt)"
echo "=========================================="
echo ""

if ! command -v certbot &> /dev/null; then
    echo "Installing Certbot..."
    sudo apt install -y certbot python3-certbot-nginx
fi

echo "Obtaining SSL certificate..."
sudo certbot certonly --nginx -d angkorfhospital.com -d www.angkorfhospital.com --agree-tos --non-interactive --expand

# Update Nginx config for HTTPS
sudo tee /etc/nginx/sites-available/angkorfhospital.com > /dev/null <<'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name angkorfhospital.com www.angkorfhospital.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name angkorfhospital.com www.angkorfhospital.com;
    root /var/www/angkorfhospital/public;

    ssl_certificate /etc/letsencrypt/live/angkorfhospital.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/angkorfhospital.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

sudo systemctl reload nginx

# ============================================================================
# DEPLOYMENT COMPLETE
# ============================================================================
echo ""
echo "=========================================="
echo "✅ DEPLOYMENT COMPLETE!"
echo "=========================================="
echo ""
echo "📋 SUMMARY:"
echo "   Domain: https://angkorfhospital.com"
echo "   PHP Version: $(php -v | head -n 1)"
echo "   Database: angkorfhospital"
echo "   Location: /var/www/angkorfhospital"
echo ""
echo "🔧 NEXT STEPS:"
echo ""
echo "1. Update DNS records to point to: 208.122.28.27"
echo ""
echo "2. Add Gmail SMTP credentials to .env:"
echo "   SSH into server and edit /var/www/angkorfhospital/.env"
echo "   Add:"
echo "   MAIL_USERNAME=voramanvuth@gmail.com"
echo "   MAIL_PASSWORD=<your-gmail-app-password>"
echo ""
echo "3. Set up database migrations (if needed):"
echo "   php artisan migrate --force"
echo ""
echo "4. Create admin user (optional):"
echo "   php artisan tinker"
echo "   > User::factory()->create();"
echo ""
echo "5. Monitor logs:"
echo "   tail -f /var/www/angkorfhospital/storage/logs/laravel.log"
echo ""
echo "6. SSL will auto-renew (check cron job):"
echo "   sudo systemctl status certbot.timer"
echo ""
echo "=========================================="
echo "Database Credentials (save securely!):"
echo "   DB_HOST: 127.0.0.1"
echo "   DB_USERNAME: clinic_user"
echo "   DB_PASSWORD: $DB_PASSWD"
echo "=========================================="
echo ""
