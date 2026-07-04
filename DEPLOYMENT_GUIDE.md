# Panduan Deployment Laravel ke cPanel (apms.whf.bz)

## Langkah 1: Setup Database di cPanel

1. Login ke cPanel Anda
2. Buka **Databases** → **MySQL Database Wizard**
3. Buat database baru:
   - Database name: `apms_production`
   - Klik "Next Step"
4. Buat database user:
   - Username: `apms_user`
   - Password: [Gunakan password yang kuat]
   - Klik "Create User"
5. Grant privileges:
   - Select "ALL PRIVILEGES"
   - Klik "Make Changes"
6. Catat informasi database:
   - Database name: `yourusername_apms_production`
   - Username: `yourusername_apms_user`
   - Password: [password yang Anda buat]
   - Host: `localhost`

## Langkah 2: Upload File ke Server

### Opsi A: Menggunakan File Manager

1. Buka **File Manager** di cPanel
2. Navigasi ke folder `public_html`
3. Hapus semua file default jika ada
4. Upload file-file berikut dari lokal:
   - **JANGAN upload**: `.git`, `node_modules`, `vendor`, `.env.backup`, `credentials`
   - **Upload semua file lainnya** termasuk:
     - `app/`
     - `bootstrap/`
     - `config/`
     - `database/`
     - `lang/`
     - `public/`
     - `resources/`
     - `routes/`
     - `storage/`
     - `tests/`
     - `vendor/` (yang sudah di-optimized)
     - `artisan`
     - `composer.json`
     - `composer.lock`
     - `.env.production` (rename jadi `.env`)
     - `.htaccess`
     - File-file root lainnya

### Opsi B: Menggunakan FTP

1. Gunakan FileZilla atau FTP client lain
2. Connect dengan FTP credentials dari cPanel
3. Upload semua file ke folder `public_html`
4. Pastikan mode transfer: Binary

## Langkah 3: Setup Environment Variables

1. Di File Manager, buka file `.env`
2. Edit dengan informasi database yang Anda catat:
   ```env
   DB_DATABASE=yourusername_apms_production
   DB_USERNAME=yourusername_apms_user
   DB_PASSWORD=your_database_password
   APP_KEY=your_generated_app_key
   APP_URL=https://apms.whf.bz
   ```
3. Generate APP_KEY jika belum ada:
   - Buka **Terminal** di cPanel atau SSH
   - Jalankan: `php artisan key:generate`
   - Copy generated key ke `.env`

## Langkah 4: Setup Permissions

Jalankan perintah berikut di Terminal cPanel atau SSH:

```bash
# Set ownership (jika bisa)
chown -R yourusername:yourusername public_html

# Set permissions
cd public_html
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Special permissions for Laravel
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/build
```

## Langkah 5: Setup Document Root

1. Buka **File Manager**
2. Pastikan document root mengarah ke folder yang benar
3. Jika perlu, buat symbolic link:
   ```bash
   # Di Terminal cPanel
   cd ~
   rm -rf public_html
   ln -s apms/public public_html
   ```
   (Jika Anda upload di folder terpisah)

## Langkah 6: Run Database Migrations

Di Terminal cPanel atau SSH:

```bash
cd ~/public_html
php artisan migrate --force
php artisan db:seed --force
```

## Langkah 7: Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Langkah 8: Setup SSL Certificate

1. Buka **SSL Certificates** di cPanel
2. Pilih domain `apms.whf.bz`
3. Install SSL certificate (Let's Encrypt gratis jika tersedia)
4. Force HTTPS:
   - Buka **File Manager**
   - Edit `.htaccess` di root
   - Tambahkan:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

## Langkah 9: Setup Cron Jobs (Optional)

Untuk scheduled tasks:

1. Buka **Cron Jobs** di cPanel
2. Add new cron job:
   - Minute: `*`
   - Hour: `*`
   - Day: `*`
   - Month: `*`
   - Weekday: `*`
   - Command: `/usr/local/bin/php /home/yourusername/public_html/artisan schedule:run >> /dev/null 2>&1`

## Langkah 10: Testing

1. Buka browser dan akses: `https://apms.whf.bz`
2. Test login dengan user yang sudah ada
3. Test fitur-fitur utama
4. Check error logs jika ada masalah:
   - `storage/logs/laravel.log`

## Troubleshooting

### Error 500
- Check file permissions
- Check `.env` configuration
- Check `storage/logs/laravel.log`

### Database Connection Error
- Verify database credentials in `.env`
- Check if database exists in cPanel
- Verify user has proper privileges

### White Screen
- Enable debug temporarily in `.env`: `APP_DEBUG=true`
- Check PHP version compatibility (PHP 8.2+ required)
- Check if all dependencies are installed

### Asset Not Loading
- Run `php artisan storage:link` jika perlu
- Check `public/build` permissions
- Clear cache: `php artisan cache:clear`

## File yang TIDAK perlu diupload

- `.git/`
- `node_modules/`
- `tests/` (optional)
- `.env.backup`
- `credentials/`
- `phpunit.xml`
- `.phpunit.result.cache`
- Screenshot files (*.png)

## File yang HARUS diupload

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `lang/`
- `public/` (termasuk `public/build/`)
- `resources/`
- `routes/`
- `storage/`
- `vendor/` (optimized version)
- `artisan`
- `composer.json`
- `composer.lock`
- `.env` (dari `.env.production`)
- `.htaccess`
- File-file root lainnya

## Backup Sebelum Deployment

Sebelum deploy, backup:
1. Database lokal
2. File `.env` lokal
3. Semua perubahan yang belum di-commit

## Catatan Penting

- PHP Version: 8.3 (sesuai dengan cPanel)
- Database: MySQL/MariaDB
- Storage: Pastikan folder `storage` writable
- Memory Limit: Set minimum 128MB di PHP Settings
- Max Execution Time: Set minimum 30 detik
- Upload Max Filesize: Set minimum 10MB (untuk upload gambar/dokumen)
