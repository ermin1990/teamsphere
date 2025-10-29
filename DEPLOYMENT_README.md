# TeamSphere MySQL Migration - Shared Hosting Deployment

## 🚀 Deployment Instructions

### Prerequisites
- Shared hosting sa PHP 8.1+ i MySQL podrškom
- FTP pristup za upload fajlova
- MySQL baza podataka kreirana na hostingu

### Step 1: Prepare Files for Upload
Upload all project files to your shared hosting via FTP, EXCEPT:
- `.env` (local development file)
- `database/database.sqlite` (SQLite database)

### Step 2: Configure Production Environment
1. Upload `.env.production` file to your hosting
2. Rename it to `.env`
3. Edit the database credentials in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost          # Usually 'localhost' on shared hosting
   DB_PORT=3306              # Usually 3306
   DB_DATABASE=your_db_name  # Your MySQL database name
   DB_USERNAME=your_username # Your MySQL username
   DB_PASSWORD=your_password # Your MySQL password
   ```

### Step 3: Run Migration
1. Upload `migrate.php` to your `public/` directory (it should be at `public/migrate.php`)
2. Open your browser and go to: `https://yourdomain.com/migrate.php`
3. Wait for migration to complete
4. **IMPORTANT**: Delete `public/migrate.php` immediately after migration!

### Step 4: Final Setup
1. Ensure file permissions are correct (755 for directories, 644 for files)
2. Test your application
3. If you encounter any issues, check the PHP error logs

## 🔧 Troubleshooting

### Common Issues:

**"Database connection failed"**
- Check your MySQL credentials in `.env`
- Ensure your hosting allows external MySQL connections
- Some shared hostings require 'localhost' instead of server IP

**"Permission denied"**
- Check file permissions (755 for folders, 644 for files)
- Ensure PHP has write access to `storage/` and `bootstrap/cache/`

**"Class not found" errors**
- Run `composer install` if your hosting supports it
- Or upload `vendor/` directory via FTP

### Security Notes:
- Never leave `migrate.php` on your server
- Change default passwords
- Keep your `.env` file secure
- Regularly update dependencies

## 📞 Support
If you encounter issues, check:
1. PHP error logs
2. Laravel logs in `storage/logs/`
3. Database connection settings
4. File permissions

## ✅ Post-Migration Checklist
- [ ] Migration completed successfully
- [ ] `migrate.php` deleted
- [ ] `.env.production` renamed to `.env`
- [ ] Database credentials updated
- [ ] Application accessible
- [ ] User registration/login works
- [ ] Data integrity verified