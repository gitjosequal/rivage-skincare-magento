# Rivage Skincare Magento 2.4.8 Deployment Guide

## 🚀 Server Requirements

### Minimum Requirements:
- **PHP**: 8.3+ (recommended: 8.3.0)
- **MySQL**: 8.0+ or MariaDB 10.4+
- **Elasticsearch**: 8.11.0+ (or OpenSearch 2.0+)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: 2GB+ RAM
- **Storage**: 20GB+ free space

### Recommended Production Setup:
- **PHP**: 8.3.0 with OPcache enabled
- **MySQL**: 8.0.35+ with InnoDB engine
- **Elasticsearch**: 8.11.0 with 2GB+ heap memory
- **Web Server**: Nginx 1.24+ with PHP-FPM
- **Memory**: 4GB+ RAM
- **Storage**: 50GB+ SSD

## 📋 Pre-Deployment Checklist

### 1. Server Environment Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml php8.3-gd php8.3-curl php8.3-intl php8.3-mbstring php8.3-zip php8.3-bcmath php8.3-soap php8.3-redis php8.3-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Elasticsearch
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb https://artifacts.elastic.co/packages/8.x/apt stable main" | sudo tee /etc/apt/sources.list.d/elastic-8.x.list
sudo apt update && sudo apt install -y elasticsearch
```

### 2. Database Setup
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
CREATE DATABASE rivage_skincare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'rivage_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON rivage_skincare.* TO 'rivage_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Elasticsearch Configuration
```bash
# Configure Elasticsearch
sudo nano /etc/elasticsearch/elasticsearch.yml

# Add these settings:
cluster.name: rivage-cluster
node.name: rivage-node-1
network.host: 127.0.0.1
http.port: 9200
discovery.type: single-node
xpack.security.enabled: false

# Start Elasticsearch
sudo systemctl enable elasticsearch
sudo systemctl start elasticsearch
```

## 🔧 Deployment Steps

### 1. Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/gitjosequal/rivage-skincare-magento.git rivage
sudo chown -R www-data:www-data rivage
cd rivage
```

### 2. Install Dependencies
```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Set proper permissions
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;
sudo chmod +x bin/magento
```

### 3. Environment Configuration
```bash
# Copy environment file
cp app/etc/env.php.sample app/etc/env.php

# Edit configuration
sudo nano app/etc/env.php
```

### 4. Database Import
```bash
# Import database (if you have a backup)
mysql -u rivage_user -p rivage_skincare < rivageae_rivage.sql
```

### 5. Magento Setup
```bash
# Set deployment mode
php bin/magento deploy:mode:set production

# Run setup upgrade
php bin/magento setup:upgrade

# Compile DI
php bin/magento setup:di:compile

# Deploy static content
php bin/magento setup:static-content:deploy -f

# Reindex
php bin/magento indexer:reindex

# Flush cache
php bin/magento cache:flush
```

### 6. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/rivage/pub;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Magento specific
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
}
```

### 7. SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 🔒 Security Configuration

### 1. File Permissions
```bash
# Set secure permissions
sudo find /var/www/rivage -type f -exec chmod 644 {} \;
sudo find /var/www/rivage -type d -exec chmod 755 {} \;
sudo chmod +x /var/www/rivage/bin/magento
sudo chown -R www-data:www-data /var/www/rivage
```

### 2. PHP Configuration
```bash
# Edit PHP configuration
sudo nano /etc/php/8.3/fpm/php.ini

# Recommended settings:
memory_limit = 2G
max_execution_time = 300
max_input_vars = 10000
post_max_size = 100M
upload_max_filesize = 100M
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
```

### 3. Firewall Setup
```bash
# Configure UFW firewall
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 9200/tcp  # Block Elasticsearch from external access
```

## 📊 Performance Optimization

### 1. Redis Configuration
```bash
# Install Redis
sudo apt install -y redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf

# Set these values:
maxmemory 512mb
maxmemory-policy allkeys-lru
```

### 2. MySQL Optimization
```bash
# Edit MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf

# Add these settings:
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 64M
query_cache_type = 1
```

### 3. Magento Cache Configuration
```bash
# Enable Redis cache
php bin/magento setup:config:set --cache-backend=redis --cache-backend-redis-server=127.0.0.1 --cache-backend-redis-db=0

# Enable Redis session storage
php bin/magento setup:config:set --session-save=redis --session-save-redis-host=127.0.0.1 --session-save-redis-db=2

# Enable Redis page cache
php bin/magento setup:config:set --page-cache=redis --page-cache-redis-server=127.0.0.1 --page-cache-redis-db=1
```

## 🔄 Automated Deployment

### 1. Create Deployment Script
```bash
#!/bin/bash
# deploy.sh

echo "Starting deployment..."

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run Magento commands
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento indexer:reindex
php bin/magento cache:flush

# Set permissions
sudo chown -R www-data:www-data .
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

echo "Deployment completed!"
```

### 2. GitHub Actions (Optional)
Create `.github/workflows/deploy.yml`:
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/rivage
          ./deploy.sh
```

## 🚨 Troubleshooting

### Common Issues:

1. **Permission Errors**: Ensure www-data owns all files
2. **Memory Issues**: Increase PHP memory_limit
3. **Elasticsearch Connection**: Check if Elasticsearch is running
4. **Static Content**: Redeploy static content after changes
5. **Cache Issues**: Clear all caches

### Useful Commands:
```bash
# Check Magento status
php bin/magento setup:db:status

# Check module status
php bin/magento module:status

# Check cache status
php bin/magento cache:status

# Check index status
php bin/magento indexer:status
```

## 📞 Support

For issues or questions:
- GitHub Issues: https://github.com/gitjosequal/rivage-skincare-magento/issues
- Documentation: Check Magento 2.4.8 official docs
- Community: Magento Community Forums

---

**Note**: This deployment guide assumes a fresh Ubuntu 20.04+ server. Adjust commands for your specific server environment.
