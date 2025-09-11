# Rivage Skincare - Magento 2.4.8 E-commerce Website

![Magento 2.4.8](https://img.shields.io/badge/Magento-2.4.8-blue)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

A complete Magento 2.4.8 e-commerce website for Rivage Skincare, featuring Dead Sea minerals products and a modern, responsive design.

## 🚀 Features

- **Magento 2.4.8** - Latest stable version with enhanced security and performance
- **PHP 8.3** - Modern PHP with improved performance and security
- **Custom Rivage Theme** - Beautiful, responsive design optimized for skincare products
- **Elasticsearch 8.11.0** - Advanced search capabilities
- **Redis Caching** - High-performance caching for better speed
- **Multi-language Support** - English and Arabic language support
- **Payment Integration** - Stripe, PayPal, and Tabby payment methods
- **SEO Optimized** - Clean URLs, meta tags, and structured data
- **Mobile Responsive** - Perfect display on all devices

## 📋 Requirements

### Minimum Requirements:
- **PHP**: 8.3.0+
- **MySQL**: 8.0+ or MariaDB 10.4+
- **Elasticsearch**: 8.11.0+
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

## 🛠️ Installation

### Option 1: Docker (Recommended for Development)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/gitjosequal/rivage-skincare-magento.git
   cd rivage-skincare-magento
   ```

2. **Start the Docker environment:**
   ```bash
   docker-compose up -d
   ```

3. **Access the application:**
   - Website: http://localhost:8080
   - Admin: http://localhost:8080/admin
   - PHPMyAdmin: http://localhost:8081

### Option 2: Manual Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/gitjosequal/rivage-skincare-magento.git
   cd rivage-skincare-magento
   ```

2. **Install dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configure environment:**
   ```bash
   cp app/etc/env.php.sample app/etc/env.php
   # Edit app/etc/env.php with your database credentials
   ```

4. **Run Magento setup:**
   ```bash
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento indexer:reindex
   php bin/magento cache:flush
   ```

## 🔧 Configuration

### Environment Configuration

Copy `app/etc/env.php.sample` to `app/etc/env.php` and update the following:

```php
'db' => [
    'connection' => [
        'default' => [
            'host' => 'localhost',
            'dbname' => 'rivage_skincare',
            'username' => 'your_username',
            'password' => 'your_password',
        ]
    ]
],
'elasticsearch' => [
    'server_hostname' => '127.0.0.1',
    'server_port' => '9200',
    'index_prefix' => 'rivage',
],
```

### Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/pub;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location / {
        try_files $uri $uri/ /index.php?$args;
    }
}
```

## 🚀 Deployment

### Production Deployment

1. **Use the deployment script:**
   ```bash
   ./deploy.sh deploy
   ```

2. **Manual deployment steps:**
   ```bash
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
   ```

### Docker Production Deployment

1. **Build and deploy:**
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

## 📊 Performance Optimization

### Redis Configuration
```bash
# Enable Redis cache
php bin/magento setup:config:set --cache-backend=redis --cache-backend-redis-server=127.0.0.1 --cache-backend-redis-db=0

# Enable Redis session storage
php bin/magento setup:config:set --session-save=redis --session-save-redis-host=127.0.0.1 --session-save-redis-db=2

# Enable Redis page cache
php bin/magento setup:config:set --page-cache=redis --page-cache-redis-server=127.0.0.1 --page-cache-redis-db=1
```

### PHP Optimization
```ini
; php.ini optimizations
memory_limit = 2G
max_execution_time = 300
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
```

## 🔒 Security

### Security Features
- **HTTPS Enforcement** - SSL/TLS encryption
- **Security Headers** - XSS protection, content type options
- **File Permissions** - Proper file and directory permissions
- **Input Validation** - Sanitized user inputs
- **SQL Injection Protection** - Prepared statements
- **CSRF Protection** - Cross-site request forgery protection

### Security Checklist
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure firewall rules
- [ ] Enable security headers
- [ ] Regular security updates
- [ ] Monitor access logs

## 🧪 Testing

### Run Tests
```bash
# Unit tests
php bin/magento dev:tests:run unit

# Integration tests
php bin/magento dev:tests:run integration

# Static tests
php bin/magento dev:tests:run static
```

## 📚 Documentation

- [Magento 2.4.8 Documentation](https://devdocs.magento.com/)
- [Deployment Guide](DEPLOYMENT.md)
- [API Documentation](https://devdocs.magento.com/guides/v2.4/rest/bk-rest.html)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- **GitHub Issues**: [Create an issue](https://github.com/gitjosequal/rivage-skincare-magento/issues)
- **Email**: support@rivage.ae
- **Documentation**: Check the [Deployment Guide](DEPLOYMENT.md)

## 🏆 Acknowledgments

- **Magento Community** - For the excellent e-commerce platform
- **Rivage Skincare** - For the beautiful product line
- **Contributors** - For their valuable contributions

## 📈 Roadmap

- [ ] Multi-store support
- [ ] Advanced analytics integration
- [ ] Mobile app API
- [ ] AI-powered product recommendations
- [ ] Advanced inventory management

---

**Made with ❤️ for Rivage Skincare**
