FROM composer:2 AS composer

FROM php:8.3

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
  && apt-get install -y libzip-dev

RUN apt-get install -y libpq-dev \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-install zip

COPY config config
COPY src src

COPY composer.json composer.json

RUN composer update

COPY phpunit.xml .
COPY .env.test .
COPY tests tests

