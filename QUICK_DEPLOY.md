# 🚀 TeamSphere Shared Hosting Deployment - Quick Start

## Files to Upload via FTP:
1. **All project files** (except `.env` and `database/database.sqlite`)
2. **`.env.production`** → rename to `.env` on server
3. **`migrate.php`** → run once via browser, then DELETE

## Step-by-Step:

### 1. Upload Files
```bash
# Upload entire project via FTP to public_html/
# Skip: .env, database/database.sqlite
```

### 2. Configure Environment
```bash
# On server, rename .env.production to .env
# Edit database credentials in .env:
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Run Migration
```bash
# Open in browser: https://yourdomain.com/migrate.php
# Wait for completion, then DELETE migrate.php
```

### 4. Test Application
```bash
# Visit: https://yourdomain.com
# Check if login/registration works
```

## ⚠️ Security Checklist:
- [ ] Delete `migrate.php` after migration
- [ ] Verify `.env` permissions (644)
- [ ] Check file permissions (755 folders, 644 files)
- [ ] Test database connection
- [ ] Remove any debug/test files

## 🆘 If Issues Occur:
1. Check PHP error logs
2. Verify MySQL credentials
3. Ensure PHP 8.1+ with MySQL extension
4. Check file permissions

---
**Generated:** October 29, 2025
**Local .env remains unchanged for development**