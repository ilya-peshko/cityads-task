FROM php:8.3.4-fpm-alpine3.18 AS php-fpm

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

COPY . .

FROM php-fpm AS php-supervisor

RUN apk add supervisor

RUN mkdir -p /var/log/supervisor

COPY ./docker/supervisord/conf.d /etc/supervisor/conf.d

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

