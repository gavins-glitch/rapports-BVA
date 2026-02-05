FROM php:8.2-apache
COPY . /var/www/html/
RUN apt-get update && apt-get install -y libzip-dev zip && docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /var/www/html && composer install
