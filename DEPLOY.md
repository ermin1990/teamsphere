Deployment steps for VPS using Docker

1) Copy `.env.production` to the server (outside of git) and fill values. Use `.env.production.example` as reference.

2) Build and run containers:

```
docker-compose -f docker-compose.prod.yml up -d --build
```

3) Generate `APP_KEY` if not set:

```
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate --force
```

4) Optionally run migrations (or set `RUN_MIGRATIONS=true` in `.env.production`):

```
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

5) Set up firewall / reverse proxy as needed. If you want to expose on port 80, change the port mapping in `docker-compose.prod.yml`.

6) To keep services running across reboots consider creating a systemd unit that runs `docker-compose -f /path/to/docker-compose.prod.yml up -d`.
