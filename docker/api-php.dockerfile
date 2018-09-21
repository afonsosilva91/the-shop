FROM php:7-fpm

COPY api/ /var/www/api

WORKDIR /var/www/api

RUN apt-get update -y && apt-get install -y \
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql