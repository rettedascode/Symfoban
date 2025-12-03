# Multi-stage Dockerfile for Symfony application
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy composer files
COPY composer.json composer.lock ./

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/symfony/var

# Production stage
FROM base AS production

# Install dependencies (no dev dependencies for production)
RUN composer install --no-dev --no-scripts --optimize-autoloader --prefer-dist

# Configure PHP-FPM
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

# Configure OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

# Development stage
FROM base AS development

# Install all dependencies (including dev)
RUN composer install --no-scripts --prefer-dist

# Configure PHP for development
RUN echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]

