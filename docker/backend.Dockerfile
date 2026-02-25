FROM php:8.1-cli

# Install system deps
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
  && rm -rf /var/lib/apt/lists/*

# Enable extensions (sqlite by default since repo uses sqlite)
RUN docker-php-ext-install pdo pdo_sqlite

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy project files (we mount volume during dev, but copying here allows composer install in image)
COPY . /var/www/html

# Install PHP deps (no-dev for production; may be adjusted per env)
RUN composer install --no-interaction --prefer-dist || true

# Generate key if not provided
RUN php artisan key:generate --force || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
