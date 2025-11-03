#!/bin/sh

# Exit on error
set -e

echo "Starting Laravel application setup..."

# Wait for database to be ready (if using PostgreSQL)
if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Waiting for PostgreSQL to be ready..."
    max_attempts=30
    attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        if pg_isready -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "$DB_USERNAME" 2>/dev/null; then
            echo "PostgreSQL is ready!"
            break
        fi
        
        attempt=$((attempt + 1))
        if [ $attempt -lt $max_attempts ]; then
            echo "PostgreSQL is unavailable - attempt $attempt/$max_attempts, sleeping..."
            sleep 2
        else
            echo "WARNING: PostgreSQL not ready after $max_attempts attempts, continuing anyway..."
        fi
    done
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
    # Clear config cache first to ensure DB_CONNECTION is correct
    php artisan config:clear
    
    # Try to run migrations with error handling
    if php artisan migrate --force --verbose 2>&1 | tee /tmp/migration.log; then
        echo "✓ Migrations completed successfully"
    else
        echo "⚠ Migration had errors - checking if they're non-critical..."
        # Check if error is about existing columns (non-critical)
        if grep -q "already exists" /tmp/migration.log; then
            echo "✓ Columns already exist, continuing..."
        else
            echo "✗ Critical migration error occurred!"
            cat /tmp/migration.log
        fi
    fi
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
