#!/usr/bin/env bash
# One-time setup on VPS: GHA deploy key + confirm paths
set -euo pipefail

KEY="$HOME/.ssh/gha_globalexchange"
AUTH="$HOME/.ssh/authorized_keys"

if [[ ! -f "$KEY" ]]; then
  ssh-keygen -t ed25519 -f "$KEY" -N "" -C "gha-globalexchange-live-deploy"
  echo "==> created $KEY"
fi

PUB="$(cat "${KEY}.pub")"
if ! grep -qxF "$PUB" "$AUTH" 2>/dev/null; then
  echo "$PUB" >> "$AUTH"
  echo "==> added to authorized_keys"
else
  echo "==> already in authorized_keys"
fi

chmod 600 "$KEY" "$AUTH"
chmod 644 "${KEY}.pub"

echo "==> PUBLIC KEY (add as GitHub secret VPS_SSH_KEY = private key contents):"
cat "${KEY}.pub"
echo
echo "==> PRIVATE KEY path: $KEY"
echo "VPS_HOST=84.247.142.251"
echo "VPS_USER=deploy"
