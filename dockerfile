FROM richarvey/php-fpm-with-nginx:latest

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permissions untuk storage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Environment khusus untuk image ini agar root folder mengarah ke /public
ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel

EXPOSE 80

# Jalankan migrations otomatis saat startup (Opsional)
CMD ["sh", "-c", "php artisan migrate --force && /start.sh"]
