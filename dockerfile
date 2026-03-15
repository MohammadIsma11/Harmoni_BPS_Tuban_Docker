FROM richarvey/php-fpm-with-nginx:latest

# Instalasi library sistem untuk GD (Excel), ZIP, dan PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_pgsql

WORKDIR /var/www/html
COPY . .

# Install dependencies dengan mengabaikan pengecekan platform agar build lancar
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV WEBROOT /var/www/html/public
ENV APP_TYPE laravel

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force && /start.sh"]
