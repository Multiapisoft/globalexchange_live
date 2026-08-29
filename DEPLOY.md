# Deploy: shared MySQL + https://dev.globalexchange.live

## Architecture

```
Internet → Caddy (SSL) → Nginx → PHP-FPM (this app)
                              ↓
                     shared-mysql (hiuser)
                       ├─ globalexchange_live
                       └─ (next project DBs…)
```

- MySQL **ek baar** start hota hai (`infra/shared-mysql`)
- Har nayi app same Docker network `shared_db_net` join karti hai
- MySQL port sirf `127.0.0.1:3306` — public internet pe open nahi

## Server pe pehli baar (Ubuntu / Debian)

### 1) DNS

`dev.globalexchange.live` → A record → server public IP  
Ports **80** aur **443** open hone chahiye (firewall / security group).

### 2) Docker install

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
# logout / login after this
```

### 3) Shared MySQL (sab projects ke liye)

```bash
cd /opt
# repo clone ke baad:
cd /path/to/globalexchange_live/infra/shared-mysql
cp .env.example .env
nano .env   # strong MYSQL_ROOT_PASSWORD + MYSQL_SHARED_PASSWORD

docker compose up -d
docker compose ps
docker exec -it shared-mysql mysqladmin ping -h 127.0.0.1 -uroot -p
```

Naya project DB add karna ho:

```bash
# edit add-database.sql.example → real DB name
docker exec -i shared-mysql mysql -uroot -p < add-database.sql
```

### 4) App live (yeh project)

```bash
cd /path/to/globalexchange_live
cp .env.example .env
cp .env.php.example .env.php
nano .env.php
# DB_HOST=shared-mysql
# DB_USERNAME=hiuser
# DB_PASSWORD=<same as MYSQL_SHARED_PASSWORD>
# baaki secrets fill karo

# SQL dump import (agar backup hai):
# docker exec -i shared-mysql mysql -uhiuser -p globalexchange_live < dump.sql

docker compose up -d --build
docker compose ps
docker compose logs -f caddy
```

Browser: `https://dev.globalexchange.live`

### 5) Local Windows (sirf test, bina domain SSL)

Shared MySQL:

```powershell
cd infra\shared-mysql
copy .env.example .env
# passwords set karo
docker compose up -d
```

App (Caddy domain SSL local pe fail ho sakta hai — pehle sirf nginx+php test):

```powershell
cd ..\..
copy .env.php.example .env.php
# DB_HOST=shared-mysql, hiuser password match karo
docker compose up -d --build
```

Local pe agar 80/443 busy ho to `docker-compose.yml` me Caddy ports change karo, ya Caddy hata ke nginx pe `ports: "8080:80"` map karo.

## Dusre PHP projects kaise join karein

1. Unke `docker-compose.yml` me:

```yaml
networks:
  shared_db_net:
    external: true
    name: shared_db_net
```

2. App service us network pe ho  
3. `DB_HOST=shared-mysql`, user `hiuser`, alag `DB_NAME`  
4. DB create: `infra/shared-mysql/add-database.sql.example`

## Useful commands

```bash
docker compose -f infra/shared-mysql/docker-compose.yml logs -f
docker compose logs -f app
docker exec -it shared-mysql mysql -uhiuser -p
```

## Security notes

- Root / hiuser passwords strong rakho; `.env` / `.env.php` commit mat karo  
- MySQL ko public `0.0.0.0:3306` pe mat bind karo  
- Wallet / SMTP keys sirf server `.env.php` me
