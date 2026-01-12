# Fix for "Forbidden" Error on Website Settings Update

## Problem

When updating Google Tag Manager code in the admin panel's Website Settings on the live server, a **403 Forbidden** error occurs. This works fine on local but fails on production.

## Root Cause

The issue is caused by:

1. **ModSecurity/WAF Rules** - Live servers often have ModSecurity or Web Application Firewall that blocks `<script>` tags in POST data
2. **Content filtering** - Security rules that prevent HTML/JavaScript code in form submissions
3. **Request size limits** - Strict POST data size restrictions

## Files Changed

### 1. `/public/.htaccess`

Added ModSecurity bypass rules to allow script tags.

### 2. `/public/livewire/.htaccess` (NEW FILE)

Created to disable ModSecurity specifically for Livewire endpoints.

### 3. `/public/.user.ini` (NEW FILE)

Increased PHP limits for larger POST data.

### 4. `/resources/views/livewire/website-data/index.blade.php`

-   Removed restrictive regex validation for script codes
-   Added better error handling with try-catch
-   Added logging for debugging

## Deployment Steps for Live Server

### Step 1: Upload Modified Files

Upload these files to your live server:

-   `public/.htaccess`
-   `public/livewire/.htaccess` (new file)
-   `public/.user.ini` (new file)
-   `resources/views/livewire/website-data/index.blade.php`

### Step 2: Clear Caches

Run these commands on your live server:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 3: Set Proper Permissions

```bash
chmod 644 public/.htaccess
chmod 644 public/livewire/.htaccess
chmod 644 public/.user.ini
```

### Step 4: If Still Getting Forbidden Error

#### Option A: Add to cPanel ModSecurity (if using cPanel)

1. Go to cPanel → ModSecurity
2. Add these rules to whitelist:
    - Rule ID: 973301, 973302, 973304, 973306, 973300, 973308, 973316
3. Or completely disable ModSecurity for the domain (temporarily for testing)

#### Option B: Contact Hosting Provider

If the above doesn't work, contact your hosting provider and request:

1. Disable ModSecurity for `/admin/website-data` route
2. Allow script tags in POST requests for admin panel
3. Increase POST data size limit to at least 50MB

#### Option C: Server-Level .htaccess (if you have root access)

Add to the main Apache configuration or VirtualHost:

```apache
<LocationMatch "^/livewire/update">
    SecRuleEngine Off
</LocationMatch>

<LocationMatch "^/admin/website-data">
    SecRuleEngine Off
</LocationMatch>
```

### Step 5: Alternative Solution - Use File Upload

If the forbidden error persists, you can implement an alternative approach:

1. Create a separate form that uploads GTM code as a `.txt` file
2. Read the file content and save to database
3. This bypasses POST data filtering

## Testing

After deployment:

1. Clear browser cache
2. Log in to admin panel
3. Go to Website Settings
4. Edit Google Tag Manager fields
5. Paste the GTM code
6. Click Submit
7. Should see success message without Forbidden error

## Logs to Check

If still failing, check these logs:

-   Laravel logs: `storage/logs/laravel.log`
-   Apache error logs: `/var/log/apache2/error.log` (path may vary)
-   ModSecurity logs: `/var/log/modsec_audit.log`

## Support

If you continue to face issues, provide:

1. Exact error message from browser console (F12 → Network tab)
2. Laravel log entries
3. Hosting provider name and plan
4. Whether you have cPanel, Plesk, or direct server access
