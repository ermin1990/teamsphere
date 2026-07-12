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
# Uses the SAME PHP version/base as the final stage (not the floating `composer:2` image's
# bundled PHP, which drifts to newer PHP releases over time and breaks `composer install`
# with platform-requirement errors against this app's locked dependencies).
FROM php:8.2-cli-alpine AS composer_builder
RUN apk add --no-cache libzip-dev zip oniguruma-dev icu-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring xml zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts
COPY . /app
RUN composer dump-autoload --optimize

### Stage 3: final image with PHP-FPM and nginx
FROM php:8.2-fpm-alpine AS final
RUN apk add --no-cache nginx bash coreutils libzip-dev zip zlib-dev oniguruma-dev icu-dev libxml2-dev curl ca-certificates \
    && docker-php-ext-install pdo_mysql mbstring xml pcntl bcmath zip

WORKDIR /var/www/html

# Copy app source and vendor
COPY --from=composer_builder /app /var/www/html

# Copy built frontend assets
COPY --from=node_builder /app/public /var/www/html/public

# nginx config - Alpine's nginx package includes /etc/nginx/conf.d/*.conf at the ROOT context
# (outside http{}) and /etc/nginx/http.d/*.conf INSIDE http{} - our server{} block must go in
# http.d, not conf.d (that's the Debian/Ubuntu convention, not Alpine's).
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# www-data already exists in this base image and is the user php-fpm's default pool config
# (php-fpm.d/www.conf) actually runs workers as - matching ownership to it here instead of
# creating a separate `www` user that php-fpm would never run as (that mismatch caused every
# runtime-written file, e.g. Blade view cache, to fail with Permission denied).
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache || true

ENV PATH="/root/.composer/vendor/bin:${PATH}"

RUN chmod +x /usr/local/bin/entrypoint.sh || true

EXPOSE 80

ENTRYPOINT ["sh","/usr/local/bin/entrypoint.sh"]
