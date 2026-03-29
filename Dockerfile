# ─── Stage: Production image ─────────────────────────────────────────────────
FROM php:8.3-fpm-alpine

LABEL maintainer="antonryazanov"
LABEL description="Book Library API — PHP 8.3 / Laravel 11"

# Install OS-level dependencies required by PHP extensions
RUN apk add --no-cache \
        git \
        curl \
        bash \
        libpng-dev \
        oniguruma-dev \
        libxml2-dev \
        zip \
        unzip \
        mysql-client

# Install PHP extensions needed by Laravel
RUN docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd

# Copy Composer binary from the official Composer image (avoids extra deps)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy only composer files first to leverage Docker layer caching —
# dependencies are only re-installed when composer files change
COPY composer.json composer.lock* ./

# Install PHP dependencies (no dev packages in production image)
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the application source code
COPY . .

# Re-run scripts now that the full application is present
RUN composer run-script post-autoload-dump --no-interaction

# Make the artisan CLI executable
RUN chmod +x artisan

# Give the web server write access to storage and cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose PHP-FPM port (Nginx connects here)
EXPOSE 9000

CMD ["php-fpm"]
