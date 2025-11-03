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
echo "==== Laravel Configuration ===="
php artisan --version
echo "APP_ENV: ${APP_ENV:-not set}"
echo "APP_KEY: ${APP_KEY:0:20}..." # Show only first 20 chars for security
echo "APP_DEBUG: ${APP_DEBUG:-not set}"
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"

# Check if .env file exists
if [ -f "/var/www/.env" ]; then
    echo ".env file: EXISTS"
else
    echo ".env file: NOT FOUND"
fi

# List storage permissions
echo "==== Storage Permissions ===="
ls -la /var/www/storage/ || echo "Storage directory issue"
ls -la /var/www/bootstrap/cache/ || echo "Bootstrap cache issue"

# Test database connection
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "==== Testing Database Connection ===="
    php artisan db:show || echo "Database connection test failed - continuing anyway"
fi

# Try to show any Laravel errors
echo "==== Testing Laravel Bootstrap ===="
php artisan env || echo "Laravel env command failed"

echo "==== Application setup complete ===="

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
