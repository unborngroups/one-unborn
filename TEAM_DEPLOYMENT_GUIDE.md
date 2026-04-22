# GSTIN Feature - Complete Deployment Guide
**Date:** November 30, 2025  
**Feature:** PAN to GSTIN Automatic Lookup Integration  
**Project:** One Unborn ISP Management System

---

## 📦 Package Information

**ZIP File:** `gstin-feature.zip`  
**Size:** ~100KB  
**Location:** `E:\Automation\one.unborn.in\one-unborn\gstin-feature.zip`

### What's Included:
- ✅ **3 Controllers** - Client, Vendor, SystemSettings
- ✅ **4 Models** - Client, Vendor, SystemSetting, Gstin
- ✅ **1 Service** - SurepassService (API integration)
- ✅ **3 Migrations** - Database structure updates
- ✅ **2 JavaScript Files** - Client & Vendor GSTIN fetch
- ✅ **5 View Files** - Updated forms
- ✅ **1 Route File** - New API endpoints
- ✅ **2 Documentation Files** - Feature guide & deployment steps

---

## 🎯 Feature Overview

### What It Does:
When creating/editing Clients or Vendors:
1. User enters **PAN number** (10 characters)
2. System **automatically fetches** all associated GSTINs from Surepass API
3. User **selects correct GSTIN** from dropdown
4. System **auto-fills**:
   - Business/Trade Name
   - Address
   - State
   - Pincode

### Benefits:
- ⚡ **Faster data entry** - No manual GSTIN lookup needed
- ✅ **Accurate data** - Direct from government database
- 🔒 **Secure** - API token stored server-side
- 📊 **History tracking** - All GSTINs saved in database

---

## 🚀 Deployment Steps for cPanel

### ⚠️ **CRITICAL: Backup First!**

**Before ANY deployment:**
1. Login to **cPanel**
2. Open **phpMyAdmin**
3. Select your database
4. Click **Export** → **Go**
5. Save `.sql` file with today's date
6. Keep this file safe for rollback if needed

---

### Step 1: Upload ZIP File

1. Login to **cPanel**
2. Click **File Manager**
3. Navigate to Laravel root (usually `public_html` or `www`)
4. Click **Upload** button (top toolbar)
5. Select `gstin-feature.zip`
6. Wait for upload (should be quick, ~100KB)

---

### Step 2: Extract Files

1. In File Manager, find `gstin-feature.zip`
2. **Right-click** → Select **Extract**
3. Confirm extraction
4. Files automatically go to correct folders
5. **Delete ZIP file** after extraction (right-click → Delete)

---

### Step 3: Run Database Migrations

**Choose ONE method:**

#### **Method A: Terminal Access** ✅ Recommended
If you have Terminal in cPanel:
```bash
cd public_html
php artisan migrate
```

Expected output:
```
Running migrations...
✓ 2025_11_29_000001_add_surepass_api_token_to_system_settings_table
✓ 2025_11_29_000002_create_gstins_table
✓ 2025_11_29_100001_add_env_and_whatsapp_to_system_settings_table_fixed
```

#### **Method B: PHP Script** (If no terminal)
1. Create file: `public/migrate.php`
2. Paste this code:
```php
<?php
// TEMPORARY MIGRATION SCRIPT - DELETE AFTER USE!
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>Running Migrations...</h2>";
echo "<pre>";

$status = $kernel->call('migrate', [
    '--force' => true
]);

echo "\n<strong>Migration completed with status: " . $status . "</strong>";
echo "</pre>";
?>
```
3. Visit: `https://yoursite.com/migrate.php`
4. Wait for "Migration completed"
5. **IMMEDIATELY DELETE** `migrate.php` (security risk!)

---

### Step 4: Clear All Caches

**Choose ONE method:**

#### **Method A: Via Terminal** ✅ Recommended
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

#### **Method B: Manual Deletion**
Using File Manager, delete contents of:
1. `bootstrap/cache/` (keep `.gitignore`)
2. `storage/framework/cache/data/`
3. `storage/framework/views/`
4. `storage/logs/laravel.log` (optional, for clean logs)

---

### Step 5: Verify File Permissions

In File Manager:
1. Navigate to `public/js/`
2. Right-click → **Change Permissions**
3. Set folder to **755**
4. Set `gstin-fetch.js` to **644**
5. Set `gstin-fetch-vendor.js` to **644**

---

### Step 6: Configure API Settings

1. **Login** to your application (admin account)
2. Navigate: **System → System Settings**
3. Scroll to **"GSTIN to PAN Settings"** section
4. Enter **API Bearer Token** (get from Surepass)
5. Select **Environment**:
   - **Sandbox** - For testing (mock data)
   - **Production** - For live PAN lookups
6. Optional: Configure **WhatsApp Settings**
7. Click **"Save Settings"**

**Where to get API token:**
- Contact Surepass support: https://surepass.io
- Or use existing token if you have one
- Sandbox tokens available for testing

---

### Step 7: Test the Feature

#### **Test 1: Verify JavaScript Files**
Open in browser:
- `https://yoursite.com/js/gstin-fetch.js`
- `https://yoursite.com/js/gstin-fetch-vendor.js`

✅ Should show JavaScript code  
❌ If shows 404 error, check file upload

#### **Test 2: Client Master**
1. Navigate: **Client Master → Add Client**
2. Enter test PAN: `AAAPL1234C`
3. Tab out or click elsewhere
4. **Expected:**
   - Loading message: "⏳ Fetching GSTIN details..."
   - List of GSTINs appears
   - Select one → fields auto-fill
5. **Verify auto-filled data:**
   - Business Name
   - Address
   - State
   - Pincode

#### **Test 3: Vendor Master**
Repeat same test in **Vendor Master → Add Vendor**

#### **Test 4: Edit Forms**
1. Open existing Client/Vendor
2. Change PAN number
3. Verify GSTIN fetch still works

---

## 🔍 Verification Checklist

After deployment, verify:

- [ ] No 404 errors on any page
- [ ] Client create/edit pages load normally
- [ ] Vendor create/edit pages load normally
- [ ] System Settings page shows new GSTIN section
- [ ] JavaScript files accessible (check URLs)
- [ ] Database has new tables: `gstins`
- [ ] Database has new columns in `system_settings`
- [ ] API token saved successfully
- [ ] GSTIN fetch works on Client form
- [ ] GSTIN fetch works on Vendor form
- [ ] Auto-fill populates correct fields
- [ ] No console errors (F12 → Console tab)

---

## 🐛 Troubleshooting Guide

### Issue: "Route [clients.fetch-gstin-by-pan] not defined"
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
php artisan config:cache
```

### Issue: "Class 'App\Models\Gstin' not found"
**Solution:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue: JavaScript not loading / Console errors
**Solutions:**
1. Clear browser cache: `Ctrl + Shift + R` (hard refresh)
2. Check file permissions: 644 for `.js` files
3. Verify file path: Files must be in `public/js/` not `resources/js/`
4. Check for typos in `<script src="">` tags

### Issue: "No GSTIN found for this PAN"
**Possible causes:**
1. **Invalid PAN** - Check format (10 characters, alphanumeric)
2. **API token not configured** - Check System Settings
3. **Wrong environment** - Switch sandbox ↔ production
4. **API quota exceeded** - Contact Surepass
5. **Server internet blocked** - Check firewall/proxy

### Issue: GSTIN fetches but doesn't auto-fill
**Solutions:**
1. Check browser console for JavaScript errors
2. Verify field IDs match:
   - Clients: `pan_number`, `gstin`, `business_display_name`, `address1`, `state`, `pincode`
   - Vendors: `pan_no`, `gstin`, `business_display_name`, `address1`, `state`, `pincode`
3. Check for JavaScript conflicts with other scripts

### Issue: "CSRF token mismatch"
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```
Then refresh browser (Ctrl + F5)

### Issue: Migration fails
**Solution:**
1. Check if tables already exist:
   ```sql
   SHOW TABLES LIKE 'gstins';
   ```
2. If exists, migration already ran
3. Check `migrations` table:
   ```sql
   SELECT * FROM migrations ORDER BY id DESC LIMIT 5;
   ```

---

## 📊 Database Changes

### New Table: `gstins`
```sql
CREATE TABLE `gstins` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(255) NOT NULL, -- 'client' or 'vendor'
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `gstin` varchar(15) NOT NULL UNIQUE,
  `trade_name` varchar(255) DEFAULT NULL,
  `principal_business_address` text DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `state_code` varchar(2) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Active',
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gstins_entity_type_entity_id_index` (`entity_type`,`entity_id`)
);
```

### Modified Table: `system_settings`
New columns added:
- `surepass_api_token` (text, nullable)
- `surepass_api_environment` (varchar, default: 'production')
- `whatsapp_default_number` (varchar, nullable)
- `whatsapp_enabled` (boolean, default: false)

---

## 🔐 Security Notes

### What's Secure:
✅ API token stored in database (not in JavaScript)  
✅ All requests use CSRF protection  
✅ No API provider names in frontend code  
✅ Server-side validation before API calls  
✅ Error messages don't expose sensitive info  

### Security Best Practices:
1. **Never share API token** in frontend code
2. **Use HTTPS** for production (required by Surepass)
3. **Rotate API tokens** periodically
4. **Monitor API usage** via Surepass dashboard
5. **Delete migration scripts** after use
6. **Restrict System Settings** access (admin only)

---

## 📈 Monitoring & Logs

### Check Application Logs:
**Location:** `storage/logs/laravel.log`

**Search for:**
- `GSTIN API Error` - API connection issues
- `Surepass` - All API-related logs
- `fetchGstinByPan` - Method execution logs

### Check Server Logs:
**cPanel → Metrics → Errors**

Look for:
- PHP errors
- Permission denied errors
- 404 errors on JavaScript files

### Check Browser Console:
**Press F12 → Console tab**

Look for:
- Network errors (red)
- JavaScript errors
- Failed fetch requests
- CORS errors

---

## 🔄 Rollback Procedure

If something goes wrong:

### Step 1: Restore Database
1. Open **phpMyAdmin**
2. Select database
3. Click **Import**
4. Choose your backup `.sql` file
5. Click **Go**

### Step 2: Restore Files
1. Delete uploaded files via File Manager
2. Upload backup files if you have them
3. Clear all caches

### Step 3: Verify
1. Check if site loads
2. Test basic functionality
3. Review error logs

---

## 📞 Support & Contacts

### Internal Support:
- **Developer:** [Your contact]
- **System Admin:** [Admin contact]

### External Support:
- **Surepass API:** support@surepass.io
- **Laravel Documentation:** https://laravel.com/docs

### Useful Links:
- Surepass Dashboard: https://surepass.io/dashboard
- API Documentation: [Check Surepass docs]
- Project Repository: https://github.com/unborngroups/one-unborn

---

## 📝 Post-Deployment Tasks

After successful deployment:

1. **Document API credentials** securely
2. **Train users** on new feature
3. **Monitor first 24 hours** for issues
4. **Check API usage** against quota
5. **Gather user feedback**
6. **Update internal documentation**

---

## 🎓 Training Notes for Team

### For End Users:
- **What changed:** PAN field now auto-fetches GSTINs
- **Action needed:** Just enter PAN, select GSTIN from list
- **Time saved:** ~2-3 minutes per client/vendor
- **Accuracy:** Data comes directly from government database

### For Support Team:
- Check System Settings has API token configured
- Verify JavaScript console if user reports issues
- Ask for PAN number to test independently
- Check Laravel logs for API errors

---

## ✅ Final Checklist for Deployment Team

**Before Deployment:**
- [ ] Database backup created and verified
- [ ] ZIP file downloaded and ready
- [ ] API token obtained from Surepass
- [ ] Maintenance window scheduled (if needed)
- [ ] Team notified of deployment

**During Deployment:**
- [ ] ZIP uploaded to correct directory
- [ ] Files extracted successfully
- [ ] Migrations ran without errors
- [ ] Caches cleared
- [ ] File permissions verified
- [ ] API token configured
- [ ] All tests passed

**After Deployment:**
- [ ] Live testing completed
- [ ] No error logs appearing
- [ ] User acceptance testing done
- [ ] Documentation updated
- [ ] Team trained
- [ ] Rollback plan ready if needed

---

**Deployment prepared by:** AI Assistant  
**Date:** November 30, 2025  
**Version:** 1.0  
**Status:** Ready for Production

---

## Questions?

If you have questions during deployment, refer to:
1. `DEPLOYMENT_STEPS.md` (in ZIP)
2. `GSTIN_FEATURE_GUIDE.md` (in ZIP)
3. This document
4. Laravel logs: `storage/logs/laravel.log`

**Good luck with the deployment! 🚀**
