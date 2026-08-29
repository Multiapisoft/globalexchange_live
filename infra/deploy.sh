#!/usr/bin/env bash
# Run on VPS: bash infra/deploy.sh [branch]
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BRANCH="${1:-master}"
cd "$ROOT"

echo "==> deploy globalexchange_live ($BRANCH) @ $(hostname)"
echo "==> cwd: $ROOT"

# Preserve secrets (never overwritten by git)
ENV_BAK=""
if [[ -f "$ROOT/.env.php" ]]; then
  ENV_BAK="$(mktemp)"
  cp "$ROOT/.env.php" "$ENV_BAK"
  echo "==> backed up .env.php"
fi

echo "==> git fetch/pull"
git fetch origin
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"
echo "HEAD: $(git rev-parse --short HEAD)"

if [[ -n "$ENV_BAK" && -f "$ENV_BAK" ]]; then
  cp "$ENV_BAK" "$ROOT/.env.php"
  rm -f "$ENV_BAK"
  echo "==> restored .env.php"
fi

if [[ ! -f "$ROOT/.env.php" ]]; then
  echo "ERROR: .env.php missing on server — aborting."
  exit 1
fi

# PHP-FPM (www-data) must read .env.php — 600 breaks the site
chmod 644 "$ROOT/.env.php"
echo "==> .env.php permissions set for php-fpm"

# Shared networks
docker network create edge 2>/dev/null || true
docker network create shared_db_net 2>/dev/null || true

# Shared MySQL (start if present, do not rebuild every push)
MYSQL_DIR="${MYSQL_DIR:-$HOME/apps/shared-mysql}"
if [[ -f "$MYSQL_DIR/docker-compose.yml" ]]; then
  echo "==> ensure shared-mysql is up"
  (cd "$MYSQL_DIR" && docker compose up -d)
fi

echo "==> docker compose up --build"
docker compose up -d --build

# Edge Caddy route (idempotent)
CADDY_FILE="${CADDY_FILE:-$HOME/apps/p2p/backend/Caddyfile}"
if [[ -f "$CADDY_FILE" ]]; then
  if ! grep -q 'dev.globalexchange.live' "$CADDY_FILE"; then
    cat >> "$CADDY_FILE" <<'CADDY'

dev.globalexchange.live {
	encode gzip
	reverse_proxy gel-nginx:80
}
CADDY
    echo "==> Caddy site added"
  fi
  (cd "$(dirname "$CADDY_FILE")" && docker compose exec -T caddy caddy reload --config /etc/caddy/Caddyfile) \
    || docker exec p2p-caddy caddy reload --config /etc/caddy/Caddyfile \
    || echo "WARN: could not reload Caddy"
fi

echo "==> status"
docker compose ps
echo "==> DONE — https://dev.globalexchange.live"
