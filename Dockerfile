FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  git zip unzip libpng-dev \
  libzip-dev default-mysql-client

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql zip gd

COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh

RUN a2enmod rewrite

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-scripts --no-autoloader && \
    composer dump-autoload --optimize

RUN php bin/console cache:clear
RUN php bin/console cache:warmup

EXPOSE 80

RUN sed -i 's!/var/www/html!/var/www/html/public!g' \
  /etc/apache2/sites-available/000-default.conf

CMD ["sh", "-c", "/usr/local/bin/wait-for-it.sh mysql:3306 -- apache2-foreground"]
  