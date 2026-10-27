FROM php:8.3-fpm-alpine

RUN apk add --no-cache imagemagick
RUN docker-php-ext-install mysqli

# Disable PHP session upload progress
RUN echo "session.upload_progress.enabled = Off" \
  > /usr/local/etc/php/conf.d/disable-upload-progress.ini

WORKDIR /var/www/html/public
