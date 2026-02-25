# Docker & Docker Compose for Teamsphere (minimal)

This repository now includes a minimal Docker setup to run both frontend and backend services locally or on a VPS.

Services included:
- frontend: builds the Vite React app and serves it with Nginx on port 80
- backend: runs Laravel using `php artisan serve` on port 8000 (exposed)

Quick start (requires Docker & Docker Compose):

1. Build and start containers:

```bash
docker compose up --build
```

2. Open frontend: http://localhost/
3. API (Laravel): http://localhost:8000

Notes & next steps:
- This is a minimal setup intended to get you running quickly on a VPS. For production-grade deployment, consider:
  - Using php-fpm + nginx for backend
  - Using a dedicated DB service (MySQL/Postgres) instead of SQLite
  - Adding health checks, supervisord or systemd for starting containers
  - Managing secrets with environment variables or a secret manager

