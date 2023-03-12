FROM php:8.2-fpm

COPY docker/99-xdebug.ini /usr/local/etc/php/conf.d/
COPY docker/bin/ /root/bin/
COPY composer.json composer.lock /var/www/html/

RUN apt update && apt install -y git unzip \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && pecl install xdebug \
    && /root/bin/composer.sh && mv composer.phar /usr/local/bin/composer && composer install
