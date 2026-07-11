#!/usr/bin/env bash
set -euo pipefail

# Generic remote deploy runner for Roboktober.
#
# Required:
#   DEPLOY_HOST     Remote host (DNS/IP), optionally with user (user@host)
# Optional:
#   DEPLOY_SSH_USER SSH user when DEPLOY_HOST has no user prefix
#   DEPLOY_SSH_PORT SSH port (default 22)
#   DEPLOY_REPO_DIR Repo directory on server (default /var/www/www.roboktober.nl)
#   DEPLOY_BRANCH   Git branch to deploy (default master)
#   DEPLOY_RUN_MIGRATIONS true/false (default true)
#   DEPLOY_BUILD_FRONTEND true/false (default false)
#   DEPLOY_PHP_BIN  Remote PHP binary override
#   DEPLOY_COMPOSER_BIN Remote Composer binary override
#   DEPLOY_DRY_RUN  true/false, prints command only (default false)

DEPLOY_HOST="${DEPLOY_HOST:-}"
DEPLOY_SSH_USER="${DEPLOY_SSH_USER:-}"
DEPLOY_SSH_PORT="${DEPLOY_SSH_PORT:-22}"
DEPLOY_REPO_DIR="${DEPLOY_REPO_DIR:-/var/www/www.roboktober.nl}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-master}"
DEPLOY_RUN_MIGRATIONS="${DEPLOY_RUN_MIGRATIONS:-true}"
DEPLOY_BUILD_FRONTEND="${DEPLOY_BUILD_FRONTEND:-false}"
DEPLOY_PHP_BIN="${DEPLOY_PHP_BIN:-}"
DEPLOY_COMPOSER_BIN="${DEPLOY_COMPOSER_BIN:-}"
DEPLOY_DRY_RUN="${DEPLOY_DRY_RUN:-false}"

log() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] $*"
}

fail() {
  echo "[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $*" >&2
  exit 1
}

[[ -n "$DEPLOY_HOST" ]] || fail "DEPLOY_HOST is required"

if [[ "$DEPLOY_HOST" != *"@"* && -n "$DEPLOY_SSH_USER" ]]; then
  TARGET="$DEPLOY_SSH_USER@$DEPLOY_HOST"
else
  TARGET="$DEPLOY_HOST"
fi

case "$DEPLOY_RUN_MIGRATIONS" in
  true|false) ;;
  *) fail "DEPLOY_RUN_MIGRATIONS must be true or false" ;;
esac

case "$DEPLOY_BUILD_FRONTEND" in
  true|false) ;;
  *) fail "DEPLOY_BUILD_FRONTEND must be true or false" ;;
esac

REMOTE_CMD="cd '$DEPLOY_REPO_DIR' && \
REPO_DIR='$DEPLOY_REPO_DIR' \
BRANCH='$DEPLOY_BRANCH' \
RUN_MIGRATIONS='$DEPLOY_RUN_MIGRATIONS' \
BUILD_FRONTEND='$DEPLOY_BUILD_FRONTEND'"

if [[ -n "$DEPLOY_PHP_BIN" ]]; then
  REMOTE_CMD+=" PHP_BIN='$DEPLOY_PHP_BIN'"
fi

if [[ -n "$DEPLOY_COMPOSER_BIN" ]]; then
  REMOTE_CMD+=" COMPOSER_BIN='$DEPLOY_COMPOSER_BIN'"
fi

REMOTE_CMD+=" bash deploy/deploy.sh"

log "Target: $TARGET"
log "Repo dir: $DEPLOY_REPO_DIR"
log "Branch: $DEPLOY_BRANCH"
log "Run migrations: $DEPLOY_RUN_MIGRATIONS"
log "Build frontend: $DEPLOY_BUILD_FRONTEND"

if [[ "$DEPLOY_DRY_RUN" == "true" ]]; then
  log "DRY RUN command: ssh -p $DEPLOY_SSH_PORT $TARGET \"$REMOTE_CMD\""
  exit 0
fi

ssh -o ServerAliveInterval=30 -p "$DEPLOY_SSH_PORT" "$TARGET" "$REMOTE_CMD"

log "Remote deploy finished successfully"
