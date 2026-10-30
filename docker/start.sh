#!/bin/sh
# Starts Samar inside the container (used by Render).
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Using a temporary key: users are logged out on every restart."
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
fi

# Render tells the app its public address.
export APP_URL="${RENDER_EXTERNAL_URL:-$APP_URL}"

php artisan package:discover --ansi
php artisan migrate --force
php artisan config:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
