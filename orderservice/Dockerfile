FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . .

# Install Laravel dependencies (fallback, will be re-installed if volume overwrites)
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts --no-interaction || true

# Give proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Expose port for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
