FROM php:8.3-fpm-alpine

RUN apk add --no-cache imagemagick
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html/public
