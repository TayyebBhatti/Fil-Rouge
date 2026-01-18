FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash git unzip icu-dev oniguruma-dev \
    libzip-dev zlib-dev \
    && docker-php-ext-install intl mbstring pdo_mysql zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D -u 1000 app
USER app
