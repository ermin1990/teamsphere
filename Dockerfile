# ==============================================================================
# Stage 1: Build frontend assets (Vite/npm)
# ==============================================================================
FROM node:24-alpine AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ==============================================================================
# Stage 2: Composer dependencies (koristi istu PHP verziju kao finalni runtime)
# ==============================================================================
FROM php:8.3-cli-alpine AS composer-builder

RUN apk add --no-cache git curl unzip postgresql-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo_pgsql zip intl mbstring bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ==============================================================================
# Stage 3: Final PHP-FPM runtime image
# ==============================================================================
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
    bash \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
        intl \
        mbstring \
        gd \
        bcmath \
        opcache

# Production php.ini tweaks
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'memory_limit=512M'; \
        echo 'upload_max_filesize=50M'; \
        echo 'post_max_size=50M'; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

# php-fpm po default-u sluša samo na 127.0.0.1:9000 kad ne radi kao root,
# a nginx mu pristupa iz DRUGOG containera preko mreže - mora slušati na 0.0.0.0
RUN { \
        echo '[www]'; \
        echo 'listen = 0.0.0.0:9000'; \
    } > /usr/local/etc/php-fpm.d/zz-listen.conf

WORKDIR /var/www/html

# Kod + composer vendor iz prethodnog stage-a
COPY --from=composer-builder /app /var/www/html

# Build-ovani frontend assets (public/build)
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Entrypoint koji pokreće migrate/cache pri startu containera
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
