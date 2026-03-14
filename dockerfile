FROM richarvey/php-fpm-with-nginx:latest

# Set working directory
WORKDIR /var/www/html

# Install system dependencies untuk GD & ZIP (diperlukan oleh PHPOffice/Excel)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

# Copy project files
COPY . .

# Install dependencies Laravel (tambahkan flag ignore-platform-reqs jika masih bandel)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-gd

# Set permissions untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Environment khusus untuk image ini
ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel

EXPOSE 80

# Jalankan migrations dan start server
CMD ["sh", "-c", "php artisan migrate --force && /start.sh"]
