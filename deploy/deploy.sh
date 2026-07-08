#!/usr/bin/env bash
set -euo pipefail

# Roboktober deployment script (Apache + Laravel)
# Usage:
#   bash deploy/deploy.sh
# Optional environment overrides:
#   REPO_DIR=/var/www/www.roboktober.nl BRANCH=master RUN_MIGRATIONS=true bash deploy/deploy.sh

REPO_DIR="${REPO_DIR:-/var/www/www.roboktober.nl}"
API_DIR="${API_DIR:-$REPO_DIR/roboktober-api}"
FRONTEND_DIR="${FRONTEND_DIR:-$REPO_DIR/roboktober-frontend}"
BRANCH="${BRANCH:-master}"
PHP_BIN="${PHP_BIN:-}"
COMPOSER_BIN="${COMPOSER_BIN:-}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
BUILD_FRONTEND="${BUILD_FRONTEND:-false}"
PHP_CANDIDATES="${PHP_CANDIDATES:-php8.6 php8.5 php8.4 php8.3 php}"
REQUIRED_PHP_EXTENSIONS="${REQUIRED_PHP_EXTENSIONS:-intl dom mbstring openssl pdo pdo_mysql tokenizer xml ctype fileinfo json}"

log() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] $*"
}

fail() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $*" >&2
  exit 1
}

resolve_php_bin() {
  if [[ -n "$PHP_BIN" ]]; then
    command -v "$PHP_BIN" >/dev/null 2>&1 || fail "PHP_BIN not found: $PHP_BIN"
    command -v "$PHP_BIN"
    return 0
  fi

  local candidate
  for candidate in $PHP_CANDIDATES; do
    if command -v "$candidate" >/dev/null 2>&1; then
      command -v "$candidate"
      return 0
    fi
  done

  fail "No PHP binary found. Checked: $PHP_CANDIDATES"
}

resolve_composer_bin() {
  if [[ -n "$COMPOSER_BIN" ]]; then
    if [[ -x "$COMPOSER_BIN" ]]; then
      echo "$COMPOSER_BIN"
      return 0
    fi
    if command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
      command -v "$COMPOSER_BIN"
      return 0
    fi
    fail "COMPOSER_BIN not found: $COMPOSER_BIN"
  fi

  local candidate
  for candidate in /usr/local/bin/composer /usr/bin/composer composer; do
    if [[ -x "$candidate" ]]; then
      echo "$candidate"
      return 0
    fi
    if command -v "$candidate" >/dev/null 2>&1; then
      command -v "$candidate"
      return 0
    fi
  done

  fail "Composer not found. Set COMPOSER_BIN explicitly."
}

check_php_extensions() {
  local missing=()
  local ext

  for ext in $REQUIRED_PHP_EXTENSIONS; do
    if ! "$PHP_BIN" -r "exit(extension_loaded('$ext') ? 0 : 1);"; then
      missing+=("$ext")
    fi
  done

  if (( ${#missing[@]} > 0 )); then
    fail "Missing required PHP extensions: ${missing[*]}"
  fi
}

run_composer() {
  "$PHP_BIN" "$COMPOSER_BIN" "$@"
}

[[ -d "$REPO_DIR/.git" ]] || fail "Repository not found at $REPO_DIR"
[[ -f "$API_DIR/artisan" ]] || fail "Laravel app not found at $API_DIR"

PHP_BIN="$(resolve_php_bin)"
COMPOSER_BIN="$(resolve_composer_bin)"

if [[ "$(id -u)" -eq 0 ]]; then
  export COMPOSER_ALLOW_SUPERUSER=1
fi

check_php_extensions

log "Deploy start"
log "Repository: $REPO_DIR"
log "Branch: $BRANCH"
log "PHP binary: $PHP_BIN"
log "PHP version: $($PHP_BIN -r 'echo PHP_VERSION;')"
log "Composer binary: $COMPOSER_BIN"

cd "$REPO_DIR"

if [[ -n "$(git status --porcelain)" ]]; then
  fail "Working tree is dirty in $REPO_DIR. Commit/stash server-local changes first."
fi

log "Fetching and updating git branch"
git fetch --all --prune
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"

log "Installing PHP dependencies"
cd "$API_DIR"
run_composer install --no-dev --optimize-autoloader --no-interaction

log "Running Laravel optimizations"
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
"$PHP_BIN" artisan event:cache
"$PHP_BIN" artisan storage:link || true

if [[ "$RUN_MIGRATIONS" == "true" ]]; then
  log "Running migrations"
  "$PHP_BIN" artisan migrate --force --no-interaction
fi

if [[ "$BUILD_FRONTEND" == "true" ]]; then
  log "Building frontend on server"
  cd "$FRONTEND_DIR"
  npm ci
  npm run build
fi

log "Restarting queue workers (if any)"
cd "$API_DIR"
"$PHP_BIN" artisan queue:restart || true

log "Deploy complete"
