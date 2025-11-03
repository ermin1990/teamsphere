#!/bin/sh

# Exit on error
set -e

echo "Starting Laravel application setup..."

# Wait for database to be ready (if using PostgreSQL)
if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Waiting for PostgreSQL to be ready..."
    until pg_isready -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "$DB_USERNAME"; do
        echo "PostgreSQL is unavailable - sleeping"
        sleep 2
    done
    echo "PostgreSQL is ready!"
fi

# Ensure storage directories exist
mkdir -p /var/www/storage/framework/{sessions,views,cache}
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run migrations (only if DATABASE_URL is set)
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration failed, continuing..."
fi

# Clear and cache config
echo "Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Don't cache in entrypoint - let app run dynamically for debugging
# php artisan config:cache
# php artisan route:cache

echo "Application setup complete!"

# Show Laravel version and environment for debugging
php artisan --version
echo "APP_ENV: ${APP_ENV:-not set}"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "APP_DEBUG: ${APP_DEBUG:-not set}"

# Test database connection
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "Testing database connection..."
    php artisan db:show || echo "Database connection test failed - continuing anyway"
fi

echo "Application setup complete!"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
