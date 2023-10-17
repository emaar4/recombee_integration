FROM php:8.1-fpm

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install calendar

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu

RUN apt-get install git unzip wait-for-it wget libicu-dev -y && \
docker-php-ext-install intl pdo_mysql && \
echo 'extension=intl.so' > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini

WORKDIR /usr/src/app

COPY --chown=1000:1000 / /usr/src/app

RUN PATH=$PATH:/usr/src/app/vendor/bin:bin

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-scripts --prefer-dist --no-interaction
