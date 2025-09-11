# Dockerfile for Rivage Skincare Magento 2.4.8
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    soap

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod +x /var/www/html/bin/magento

# Copy configuration files
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create necessary directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /var/www/html/var/log \
    && mkdir -p /var/www/html/var/cache \
    && mkdir -p /var/www/html/var/session \
    && mkdir -p /var/www/html/pub/static \
    && mkdir -p /var/www/html/pub/media \
    && mkdir -p /var/www/html/generated

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/var \
    && chown -R www-data:www-data /var/www/html/pub/static \
    && chown -R www-data:www-data /var/www/html/pub/media \
    && chown -R www-data:www-data /var/www/html/generated

# Expose port
EXPOSE 80 443

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
