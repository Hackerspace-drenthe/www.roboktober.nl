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
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
BUILD_FRONTEND="${BUILD_FRONTEND:-false}"

log() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] $*"
}

fail() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $*" >&2
  exit 1
}

[[ -d "$REPO_DIR/.git" ]] || fail "Repository not found at $REPO_DIR"
[[ -f "$API_DIR/artisan" ]] || fail "Laravel app not found at $API_DIR"

log "Deploy start"
log "Repository: $REPO_DIR"
log "Branch: $BRANCH"

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
"$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction

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
