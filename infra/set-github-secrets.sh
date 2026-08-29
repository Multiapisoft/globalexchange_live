#!/usr/bin/env bash
set -euo pipefail

extract_token() {
  python3 - <<'PY'
from pathlib import Path
text = Path.home().joinpath(".git-credentials").read_text()
for line in text.splitlines():
    if "github.com" not in line or "://" not in line:
        continue
    mid = line.split("://", 1)[1]
    cred, _host = mid.rsplit("@", 1)
    _user, token = cred.split(":", 1)
    print(token)
    break
PY
}

TOKEN="$(extract_token)"
if [[ -z "$TOKEN" ]]; then
  echo "ERROR: no github token in ~/.git-credentials"
  exit 1
fi

export GH_TOKEN="$TOKEN"
echo "auth as: $(gh api user --jq .login)"

REPO="Multiapisoft/globalexchange_live"
gh secret set VPS_HOST -R "$REPO" --body "84.247.142.251"
gh secret set VPS_USER -R "$REPO" --body "deploy"
gh secret set VPS_SSH_KEY -R "$REPO" < "$HOME/.ssh/gha_globalexchange"
gh secret list -R "$REPO"
echo SECRETS_OK
