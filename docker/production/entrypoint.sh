#!/bin/sh
set -e

echo "Running Laravel cache commands..."

# Cache config, routes, and events on container startup
php artisan config:cache
php artisan route:cache
php artisan event:cache

# If a command was passed (e.g., "php artisan horizon"), run it
# Otherwise, start supervisord for the web container
if [ $# -gt 0 ]; then
    echo "Running command: $@"
    exec "$@"
else
    echo "Starting supervisor..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
