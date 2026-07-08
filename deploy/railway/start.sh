#!/usr/bin/env sh
set -eu

APP_DIR="/var/www/html/roboktober-api"
PORT="${PORT:-8080}"

cd "$APP_DIR"

if [ -z "${APP_KEY:-}" ]; then
  echo "ERROR: APP_KEY is not set. Configure APP_KEY in Railway variables."
  exit 1
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  echo "Running migrations (--force)..."
  php artisan migrate --force
fi

# Ensure cached config/routes/views are rebuilt for the current environment.
php artisan optimize:clear >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Laravel on 0.0.0.0:${PORT}"
exec php -d variables_order=EGPCS -S 0.0.0.0:"${PORT}" -t public public/index.php