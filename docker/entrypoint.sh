#!/bin/bash

# Database migrations
php artisan migrate --force

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permission fix
chmod -R 775 storage bootstrap/cache

echo "Laravel application ready!"
