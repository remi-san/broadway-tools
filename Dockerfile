FROM php:5.5-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app