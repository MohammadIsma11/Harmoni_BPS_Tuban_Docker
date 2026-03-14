FROM richarvey/php-fpm-with-nginx:latest

# 1. Pastikan system dependencies lengkap untuk GD, ZIP, dan PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_pgsql

# 2. Set working directory
WORKDIR /var/www/html

# 3. Copy file project
COPY . .

# 4. Install dependencies dengan mengabaikan pengecekan platform sementara 
# agar build tidak berhenti di tengah jalan
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 5. Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Konfigurasi Environment Railway
ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel

EXPOSE 80

# 7. Jalankan migration dan start server
CMD ["sh", "-c", "php artisan migrate --force && /start.sh"]
