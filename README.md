# globalexchange_live

PHP app for **https://dev.globalexchange.live**

## Docker (recommended)

1. Shared MySQL (all projects): see `infra/shared-mysql/`
2. This app + SSL: `docker compose up -d --build`
3. Full steps: [DEPLOY.md](DEPLOY.md)

Local XAMPP: keep `.env.php` with `DB_HOST=localhost`.  
Docker server: use `DB_HOST=shared-mysql` and user `hiuser` (see `.env.php.example`).
