# GSTIN Feature - cPanel Deployment Guide

## Step 1: Create ZIP File (Local Machine)

### Files to Include in ZIP:
```
gstin-feature.zip
├── app/
│   ├── Http/Controllers/
│   │   ├── ClientController.php
│   │   ├── VendorController.php
│   │   └── SystemSettingsController.php
│   ├── Models/
│   │   ├── Client.php
│   │   ├── Vendor.php
│   │   ├── SystemSetting.php
│   │   └── Gstin.php
│   └── Services/
│       └── SurepassService.php
├── database/migrations/
│   ├── 2025_11_29_000001_add_surepass_api_token_to_system_settings_table.php
│   ├── 2025_11_29_000002_create_gstins_table.php
│   └── 2025_11_29_100001_add_env_and_whatsapp_to_system_settings_table_fixed.php
├── public/js/
│   ├── gstin-fetch.js
│   └── gstin-fetch-vendor.js
├── resources/views/
│   ├── clients/
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── vendors/
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── settings/
│       └── system.blade.php
├── routes/
│   └── web.php
└── GSTIN_FEATURE_GUIDE.md
```

## Step 2: Upload via cPanel

1. **Login to cPanel**
2. **Go to File Manager**
3. **Navigate to your Laravel root directory** (usually `public_html` or `www`)
4. **Upload the ZIP file**
5. **Extract the ZIP file** - it will overwrite existing files with new versions

## Step 3: Run Migrations via cPanel Terminal

### Option A: Using cPanel Terminal (if available)
1. Go to **cPanel → Terminal**
2. Navigate to project root:
   ```bash
   cd public_html
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```

### Option B: Using SSH (if Terminal not available)
1. Connect via SSH:
   ```bash
   ssh username@yourserver.com
   ```
2. Navigate to project:
   ```bash
   cd ~/public_html
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```

### Option C: Using PHP Script (if no terminal access)
Create `migrate.php` in public folder:
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('migrate');
echo "Migration completed with status: " . $status;
```

Then visit: `https://yoursite.com/migrate.php`
**DELETE THIS FILE AFTER USE!**

## Step 4: Clear Caches via cPanel

### Option A: Via Terminal
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Option B: Delete Cache Files Manually
Using File Manager, delete these directories:
- `bootstrap/cache/*.php` (except `.gitignore`)
- `storage/framework/cache/data/*`
- `storage/framework/views/*`

## Step 5: Configure System Settings

1. **Login to your application** as admin
2. **Go to: System → System Settings**
3. **Scroll to "GSTIN to PAN Settings" section**
4. **Enter API Bearer Token** from Surepass
5. **Select Environment**: Production or Sandbox
6. **Save Settings**

## Step 6: Test the Feature

1. **Go to Client Master → Add Client**
2. **Enter a PAN number** (10 characters)
3. **Tab out of the PAN field**
4. **Should see:** "Fetching GSTIN details..."
5. **After fetch:** Select GSTIN from list
6. **Verify:** Auto-fills business name, address, pincode

## Step 7: Verify JavaScript Files

Check these URLs in browser:
- `https://yoursite.com/js/gstin-fetch.js`
- `https://yoursite.com/js/gstin-fetch-vendor.js`

Both should display JavaScript code (not 404 error)

## Troubleshooting

### If GSTIN not fetching:
1. Check API token is saved in System Settings
2. Check browser console (F12) for JavaScript errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify internet connectivity from server
5. Test API token directly using Postman/cURL

### If routes not working (404 errors):
```bash
php artisan route:cache
php artisan config:cache
```

### If views not updating:
```bash
php artisan view:clear
```

### If JavaScript not loading:
- Clear browser cache (Ctrl+F5)
- Check file permissions: `chmod 644 public/js/*.js`
- Verify file exists in correct location

## Database Backup (Before Migration)

**IMPORTANT:** Always backup database before running migrations!

Via cPanel:
1. **cPanel → phpMyAdmin**
2. **Select your database**
3. **Click "Export" tab**
4. **Click "Go" button**
5. **Save the SQL file**

## Rollback (If Something Goes Wrong)

1. **Restore database backup** via phpMyAdmin
2. **Delete uploaded files** and restore old versions
3. **Clear all caches**

## Support

For issues, check:
- Laravel error log: `storage/logs/laravel.log`
- PHP error log: `cpanel → Metrics → Errors`
- Browser console: F12 → Console tab

---

**Created:** November 30, 2025
**Version:** 1.0
