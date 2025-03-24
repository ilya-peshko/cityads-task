FROM php:8.3.4-fpm-alpine3.18 AS app_php

WORKDIR /srv/app

RUN apk update \
    && apk add libzip-dev \
      libpq-dev \
    && docker-php-ext-install \
      opcache \
      pcntl \
      zip \
      pdo \
      pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
