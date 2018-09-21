FROM php:7-fpm

COPY api/ /var/www/api
COPY docker/api-php.sh /scripts/api-php.sh

WORKDIR /var/www/api

RUN apt-get update -y && apt-get install -y \
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql \
    && chmod +x /scripts/*.sh \
    && php-fpm -d

CMD [ "/scritps/api-php.sh" ]