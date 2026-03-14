FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

WORKDIR /var/www

COPY . .

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
