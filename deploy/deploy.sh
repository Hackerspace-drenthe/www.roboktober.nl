#!/usr/bin/env bash
set -euo pipefail

# Roboktober deployment script (Apache + Laravel)
# Usage:
#   bash deploy/deploy.sh
# Optional environment overrides:
#   REPO_DIR=/var/www/www.roboktober.nl BRANCH=master RUN_MIGRATIONS=true bash deploy/deploy.sh
#
# Before every deploy (regardless of RUN_MIGRATIONS) a mysqldump backup of the
# database is taken (based on roboktober-api/.env). Override with:
#   BACKUP_ENABLED=false                         # skip backup entirely
#   BACKUP_DIR=/var/backups/roboktober-mysql      # where dumps are stored
#   BACKUP_RETENTION_COUNT=14                     # how many dumps to keep
#   MYSQLDUMP_BIN=/usr/bin/mysqldump              # override binary path

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
BACKUP_ENABLED="${BACKUP_ENABLED:-true}"
BACKUP_DIR="${BACKUP_DIR:-/var/backups/roboktober-mysql}"
BACKUP_RETENTION_COUNT="${BACKUP_RETENTION_COUNT:-14}"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"

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
    ensure_php_extensions "$PHP_BIN"
    command -v "$PHP_BIN"
    return 0
  fi

  local candidate
  for candidate in $PHP_CANDIDATES; do
    if command -v "$candidate" >/dev/null 2>&1; then
      local candidate_path
      candidate_path="$(command -v "$candidate")"
      if ensure_php_extensions "$candidate_path"; then
        command -v "$candidate"
        return 0
      fi
    fi
  done

  fail "No PHP binary with required extensions found. Checked: $PHP_CANDIDATES"
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

ensure_php_extensions() {
  local php_bin="$1"
  local missing=()
  local ext

  for ext in $REQUIRED_PHP_EXTENSIONS; do
    if ! "$php_bin" -r "exit(extension_loaded('$ext') ? 0 : 1);"; then
      missing+=("$ext")
    fi
  done

  if (( ${#missing[@]} > 0 )); then
    return 1
  fi

  return 0
}

run_composer() {
  "$PHP_BIN" "$COMPOSER_BIN" "$@"
}

# Reads a single KEY=value from a .env file, stripping surrounding quotes.
read_env_value() {
  local file="$1" key="$2" line
  line="$(grep -E "^${key}=" "$file" | tail -n1 || true)"
  [[ -n "$line" ]] || { echo ""; return 0; }
  line="${line#*=}"
  line="${line%\"}"; line="${line#\"}"
  line="${line%\'}"; line="${line#\'}"
  echo "$line"
}

prune_old_backups() {
  local count
  count="$(find "$BACKUP_DIR" -maxdepth 1 -name '*.sql.gz' -type f | wc -l)"
  if (( count > BACKUP_RETENTION_COUNT )); then
    find "$BACKUP_DIR" -maxdepth 1 -name '*.sql.gz' -type f -printf '%T@ %p\n' \
      | sort -n \
      | head -n "$((count - BACKUP_RETENTION_COUNT))" \
      | cut -d' ' -f2- \
      | xargs -r rm -f
    log "Pruned old backups, keeping last $BACKUP_RETENTION_COUNT"
  fi
}

backup_database() {
  if [[ "$BACKUP_ENABLED" != "true" ]]; then
    log "Database backup skipped (BACKUP_ENABLED=false)"
    return 0
  fi

  local env_file="$API_DIR/.env"
  [[ -f "$env_file" ]] || fail "Cannot back up database: $env_file not found"

  local db_connection db_host db_port db_database db_username db_password
  db_connection="$(read_env_value "$env_file" DB_CONNECTION)"

  if [[ "$db_connection" != "mysql" && "$db_connection" != "mariadb" ]]; then
    log "Database backup skipped (DB_CONNECTION=$db_connection, not mysql/mariadb)"
    return 0
  fi

  db_host="$(read_env_value "$env_file" DB_HOST)"
  db_port="$(read_env_value "$env_file" DB_PORT)"
  db_database="$(read_env_value "$env_file" DB_DATABASE)"
  db_username="$(read_env_value "$env_file" DB_USERNAME)"
  db_password="$(read_env_value "$env_file" DB_PASSWORD)"

  [[ -n "$db_database" ]] || fail "DB_DATABASE not set in $env_file, cannot back up"
  command -v "$MYSQLDUMP_BIN" >/dev/null 2>&1 || fail "mysqldump not found (set MYSQLDUMP_BIN or install mysql-client)"

  mkdir -p "$BACKUP_DIR"
  chmod 700 "$BACKUP_DIR"

  local timestamp backup_file
  timestamp="$(date +'%Y%m%d-%H%M%S')"
  backup_file="$BACKUP_DIR/${db_database}-${timestamp}.sql.gz"

  log "Backing up database '$db_database' to $backup_file"

  MYSQL_PWD="$db_password" "$MYSQLDUMP_BIN" \
    --host="${db_host:-127.0.0.1}" \
    --port="${db_port:-3306}" \
    --user="$db_username" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    "$db_database" | gzip > "$backup_file" \
    || fail "Database backup failed, aborting deploy"

  chmod 600 "$backup_file"
  log "Database backup complete: $backup_file ($(du -h "$backup_file" | cut -f1))"

  prune_old_backups
}

[[ -d "$REPO_DIR/.git" ]] || fail "Repository not found at $REPO_DIR"
[[ -f "$API_DIR/artisan" ]] || fail "Laravel app not found at $API_DIR"

PHP_BIN="$(resolve_php_bin)"
COMPOSER_BIN="$(resolve_composer_bin)"

if [[ "$(id -u)" -eq 0 ]]; then
  export COMPOSER_ALLOW_SUPERUSER=1
fi

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

log "Backing up database before deploy"
backup_database

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
