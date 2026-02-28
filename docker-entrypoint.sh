#!/bin/bash
set -e

echo "Starting Laravel initialization..."

cd /var/www/html

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Generate application key if APP_KEY is empty in .env
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations only for the main app container
if [ "$1" = "apache2-foreground" ]; then
    echo "Running migrations..."
    # Wait a few seconds for DB to be ready
    sleep 5
    php artisan migrate --force

    # Create storage link if it doesn't exist
    if [ ! -d "public/storage" ]; then
        echo "Creating storage link..."
        php artisan storage:link
    fi
fi

# Fix permissions
chown -R www-data:www-data storage bootstrap/cache

echo "Initialization complete. Starting application..."
exec "$@"