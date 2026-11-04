FROM php:8.4-apache
RUN apt-get update && apt-get install -y unzip libssl-dev
RUN pecl install mongodb && docker-php-ext-enable mongodb
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

ENV APACHE_DOCUMENT_ROOT=/var/www/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf