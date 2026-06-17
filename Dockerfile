# Multi-stage Dockerfile for production Laravel app

### Stage 1: build frontend assets with Node
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --silent
COPY resources resources
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

### Stage 2: install PHP dependencies with Composer
FROM composer:2 AS composer_builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts
COPY . /app
RUN composer dump-autoload --optimize

### Stage 3: final image with PHP-FPM and nginx
FROM php:8.1-fpm-alpine AS final
RUN apk add --no-cache nginx bash coreutils libzip-dev zip zlib-dev oniguruma-dev icu-dev libxml2-dev curl ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring xml tokenizer pcntl bcmath zip

WORKDIR /var/www/html

# Copy app source and vendor
COPY --from=composer_builder /app /var/www/html

# Copy built frontend assets
COPY --from=node_builder /app/public /var/www/html/public

# nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN addgroup -g 1000 www && adduser -D -u 1000 -G www www
RUN chown -R www:www /var/www/html && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

ENV PATH="/root/.composer/vendor/bin:${PATH}"

RUN chmod +x /usr/local/bin/entrypoint.sh || true

EXPOSE 80

ENTRYPOINT ["sh","/usr/local/bin/entrypoint.sh"]
