# Railway Environment Variables Setup Guide

## 🔑 Required Environment Variables

Copy these variables to Railway Dashboard → Your Service → Variables:

```bash
# Application
APP_NAME=TeamSphere
APP_ENV=production
APP_KEY=base64:L7FgKPDXKIhr2rvF5QfMsQYoJAD6VzGD7g6sYh2vDeE=
APP_DEBUG=false
APP_URL=https://teamsphere-production.up.railway.app

# Database - PostgreSQL (Railway provides DATABASE_URL automatically)
# Use this if Railway already provides DATABASE_URL:
DATABASE_URL=postgresql://postgres:djglFGhffLhWQHeVeWQtSKSSdOcTyqbV@maglev.proxy.rlwy.net:31352/railway

# OR set these individual variables (Railway will use them):
DB_CONNECTION=pgsql
DB_HOST=maglev.proxy.rlwy.net
DB_PORT=31352
DB_DATABASE=railway
DB_USERNAME=postgres
DB_PASSWORD=djglFGhffLhWQHeVeWQtSKSSdOcTyqbV

# Cache & Sessions (using file for now, can switch to Redis later)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (configure later if needed)
MAIL_MAILER=log

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter
```

## 📋 How to Set Variables in Railway

1. Go to Railway Dashboard
2. Select your `teamsphere-production` service
3. Click on "Variables" tab
4. Click "+ New Variable"
5. Add each variable from above (one by one or use RAW Editor)

## 🚀 After Setting Variables

1. Railway will automatically redeploy
2. Check logs for successful startup
3. Visit your app: https://teamsphere-production.up.railway.app
4. Run migrations if needed

## 🔍 Verification

After deployment, check:
- `/test.php` - Should show all variables as SET
- `/info.php` - Should show PHP configuration
- `/` - Should load Laravel homepage

## ⚠️ Important Notes

- **APP_KEY**: Generated above, unique to this deployment
- **DB_HOST**: External Railway proxy (accessible from outside)
- **DATABASE_URL**: Railway provides this automatically
- Change `APP_DEBUG=true` temporarily if you need to see detailed errors
