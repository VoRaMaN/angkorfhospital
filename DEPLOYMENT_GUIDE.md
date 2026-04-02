# ANGKOR HOSPITAL DEPLOYMENT GUIDE
## VPS Deployment on Ubuntu 24.04 LTS

---

## 📋 DEPLOYMENT CHECKLIST

### Pre-Deployment ✓
- [x] VPS IP: 208.122.28.27
- [x] Domain: angkorfhospital.com
- [x] OS: Ubuntu 24.04 LTS
- [x] PHP: 8.3+
- [x] HTTPS: Let's Encrypt (Certbot)
- [x] Repository: Public GitHub (https://github.com/VoRaMaN/angkorfhospital)
- [ ] DNS configured to point to VPS IP

### Deployment Process ✓
All automated in `deploy.sh`

---

## 🚀 QUICK START - HOW TO DEPLOY

### Step 1: SSH into Your VPS
```bash
ssh root@208.122.28.27
```

### Step 2: Download and Run Deployment Script
```bash
cd /tmp
wget https://raw.githubusercontent.com/VoRaMaN/angkorfhospital/main/deploy.sh
chmod +x deploy.sh
sudo bash ./deploy.sh
```

**OR** if you have the repo already cloned locally:
```bash
cd /var/www/angkorfhospital
chmod +x deploy.sh
sudo bash ./deploy.sh
```

### Step 3: Wait for Script to Complete
The script will:
1. ✅ Update system packages
2. ✅ Install PHP 8.3 + extensions
3. ✅ Install Nginx
4. ✅ Install MySQL
5. ✅ Create database & user
6. ✅ Clone your repository
7. ✅ Install Composer dependencies
8. ✅ Configure Laravel (.env)
9. ✅ Run migrations
10. ✅ Setup Nginx config
11. ✅ Install SSL certificate

**Total time: ~10-15 minutes**

### Step 4: Save Database Credentials
The script will output your database password. Save it somewhere secure!

**Database Details:**
- **Host:** 127.0.0.1
- **Database:** angkorfhospital
- **User:** clinic_user
- **Password:** *(generated securely during deployment)*

### Step 5: Configure Email (Gmail SMTP)
After deployment, SSH into the server and edit `.env`:

```bash
ssh root@208.122.28.27
nano /var/www/angkorfhospital/.env
```

Find these lines and update:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=voramanvuth@gmail.com
MAIL_PASSWORD=<your-gmail-app-password>
MAIL_FROM_ADDRESS=voramanvuth@gmail.com
MAIL_FROM_NAME=Angkor Hospital
```

**⚠️ Important:** If you have 2-Factor Authentication enabled on Gmail, use an **App Password** instead of your regular password.

[How to generate Gmail App Password](https://support.google.com/accounts/answer/185833)

Then restart PHP-FPM:
```bash
sudo systemctl restart php8.3-fpm
```

### Step 6: Update DNS Records
Point your domain to the VPS IP:
- **A Record:** `angkorfhospital.com` → `208.122.28.27`
- **A Record:** `www.angkorfhospital.com` → `208.122.28.27`

Give DNS 24-48 hours to propagate, or check immediately [here](https://mxtoolbox.com/mxlookup.aspx)

### Step 7: Test Your Deployment
Visit: **https://angkorfhospital.com**

You should see your Laravel application!

---

## 🔍 POST-DEPLOYMENT VERIFICATION

### Check Services are Running
```bash
# Check Nginx
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.3-fpm

# Check MySQL
sudo systemctl status mysql
```

### Check Application Logs
```bash
# Real-time logs
tail -f /var/www/angkorfhospital/storage/logs/laravel.log

# Full log
cat /var/www/angkorfhospital/storage/logs/laravel.log
```

### Check Permissions
```bash
ls -la /var/www/angkorfhospital/storage
ls -la /var/www/angkorfhospital/bootstrap/cache
```

### Test Database Connection
```bash
ssh root@208.122.28.27
mysql -u clinic_user -p -h 127.0.0.1 angkorfhospital
```

Then type the password provided by the deployment script.

---

## 🛠️ MAINTENANCE COMMANDS

### Clear Cache
```bash
ssh root@208.122.28.27

cd /var/www/angkorfhospital

# Clear all caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### View Application Logs
```bash
tail -f /var/www/angkorfhospital/storage/logs/laravel.log
```

### Run Migrations (if updates needed)
```bash
cd /var/www/angkorfhospital
php artisan migrate --force
```

### Restart Services
```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Restart MySQL
sudo systemctl restart mysql
```

---

## 🔒 SECURITY CHECKLIST

- [x] HTTPS enabled (Let's Encrypt)
- [x] PHP debug mode disabled in production
- [x] Strong database credentials
- [x] Proper file permissions (storage & cache)
- [x] Nginx security headers configured
- [ ] SSH key-based authentication (recommended)
- [ ] Firewall rules configured
- [ ] Regular backups scheduled

### Optional Security Improvements

**Disable root login over SSH:**
```bash
sudo nano /etc/ssh/sshd_config
# Change: PermitRootLogin no
sudo systemctl restart ssh
```

**Setup UFW Firewall:**
```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 22/tcp      # SSH
sudo ufw allow 80/tcp      # HTTP
sudo ufw allow 443/tcp     # HTTPS
sudo ufw enable
```

---

## 🆘 TROUBLESHOOTING

### "502 Bad Gateway" Error
**Solution:** PHP-FPM might have crashed
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl status php8.3-fpm
```

### "Connection refused" on Database
**Solution:** Check MySQL is running
```bash
sudo systemctl status mysql
sudo systemctl restart mysql
```

### "Permission denied" on storage folder
**Solution:** Reset permissions
```bash
cd /var/www/angkorfhospital
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### SSL Certificate Issues
**Renew certificate:**
```bash
sudo certbot renew --dry-run  # Test
sudo certbot renew            # Actual renewal
```

### Application Not Showing
**Check logs:**
```bash
tail -f /var/www/angkorfhospital/storage/logs/laravel.log
cat /var/log/nginx/error.log
```

---

## 📞 SUPPORT RESOURCES

- [Laravel Documentation](https://laravel.com/docs)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [Let's Encrypt/Certbot](https://certbot.eff.org/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Ubuntu Help](https://ubuntu.com/support)

---

## ✅ DEPLOYMENT STATUS

| Component | Status | Notes |
|-----------|--------|-------|
| Server Preparation | Ready | Script handles all steps |
| PHP 8.3 | Ready | Auto-installed |
| Nginx | Ready | Auto-configured |
| MySQL | Ready | Auto-setup with secure password |
| Laravel Setup | Ready | .env auto-configured |
| SSL/HTTPS | Ready | Let's Encrypt (Certbot) |
| Email | ⚠️ Manual | Requires Gmail credentials |
| DNS | ⚠️ Manual | Point to 208.122.28.27 |

---

**Last Updated:** April 3, 2026  
**Deployment Type:** VPS (LEMP Stack)  
**Domain:** angkorfhospital.com  
**IP Address:** 208.122.28.27
