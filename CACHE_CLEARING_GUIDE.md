# 🚀 Cache Clearing Guide for Live Server (cPanel)

## ⚠️ THE PROBLEM
Your `.htaccess` had `immutable` cache directive, meaning browsers **never check** for updated images. PNG images were cached for 1 year!

## ✅ THE FIX (Step-by-Step)

### **Step 1: Push Updated .htaccess to Live Server**

```bash
# Already done! The .htaccess has been updated locally
# Now push it to production:
git add public/.htaccess
git commit -m "Remove immutable cache to allow WebP migration"
git push
```

### **Step 2: Pull Changes on Live Server**

SSH into your server or use cPanel Terminal:

```bash
cd /path/to/your/website  # e.g., /home/username/public_html
git pull origin main
```

### **Step 3: Clear ALL Laravel Caches**

Run these commands on the live server:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### **Step 4: Regenerate WebP Images**

You have custom commands for this! Run:

```bash
# Option A: Regenerate all WebP safely
php artisan webp:regenerate-safely

# Option B: Regenerate synchronously (if option A doesn't exist)
php artisan webp:regenerate-sync

# Option C: Run the general regeneration
php artisan webp:regenerate
```

### **Step 5: Clear OPcache (PHP Cache)**

**Option A: Via cPanel**
1. Log into cPanel
2. Go to **"Select PHP Version"** or **"MultiPHP Manager"**
3. Click **"Options"** or **"Switch to PHP Options"**
4. Find **"opcache"** section
5. Toggle **opcache.enable** OFF then ON
6. Or click **"Reset OPcache"** if available

**Option B: Via Command Line (if available)**
```bash
php -r "opcache_reset();"
```

**Option C: Create a temporary PHP file**
1. Create file: `public/clear-opcache.php`
2. Add content:
```php
<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared!";
} else {
    echo "OPcache not available";
}
```
3. Visit: `https://yourdomain.com/clear-opcache.php`
4. Delete the file immediately after!

###Step 6: Clear LiteSpeed Cache (if using LiteSpeed)**

**Via cPanel:**
1. Look for **"LiteSpeed Web Cache Manager"**
2. Click **"Flush All"** or **"Purge All"**

**Via .htaccess:** (Already disabled in your config, but just in case)
Add this temporarily at the top of `.htaccess`:
```apache
<IfModule LiteSpeed>
CacheLookup off
</IfModule>
```

### **Step 7: Clear CDN Cache (if using Cloudflare/etc.)**

**If using Cloudflare:**
1. Log into Cloudflare dashboard
2. Go to **Caching** → **Configuration**
3. Click **"Purge Everything"**
4. Wait 5 minutes

### **Step 8: Verify WebP Files Exist**

Check if WebP files were generated:

```bash
# Check main product images
ls -la storage/app/public/*/conversions/*webp | head -20

# Count WebP files
find storage/app/public -name "*.webp" | wc -l

# Check banner images
ls -la storage/app/public/banners/*webp
```

### **Step 9: Force Browser Cache Refresh (For Testing)**

**Your browser:**
- **Chrome/Edge:** `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)
- **Firefox:** `Ctrl+F5` (Windows) or `Cmd+Shift+R` (Mac)
- **Safari:** `Cmd+Option+R`

**Or use Incognito/Private mode** to bypass all cache

---

## 🔍 DEBUGGING: If Images Still Show as PNG

### Check 1: Verify WebP Files Exist on Server
```bash
# Check a specific product image
php artisan tinker
>>> $product = App\Models\Product::first();
>>> $product->getFirstMediaUrl('main_image', 'large_webp');
# Should output a .webp URL
```

### Check 2: Check Storage Disk Permissions
```bash
chmod -R 755 storage/app/public
chown -R www-data:www-data storage/app/public  # Or your web server user
```

### Check 3: Verify Symbolic Link
```bash
# Check if storage link exists
ls -la public/storage

# If not, create it:
php artisan storage:link
```

### Check 4: Check Browser Network Tab
1. Open browser DevTools (F12)
2. Go to **Network** tab
3. Reload page
4. Filter by **Img**
5. Click on an image
6. Check **Response Headers** → Look for `Content-Type: image/webp`

### Check 5: Test Direct URL
Try accessing a WebP image directly:
```
https://yourdomain.com/storage/1/conversions/filename-large_webp.webp
```

---

## 🎯 EXPECTED RESULTS

After completing all steps:

✅ All product images should be served as WebP  
✅ Banner images should be served as WebP  
✅ Browser Network tab shows `image/webp` content type  
✅ File sizes should be 25-35% smaller  
✅ Page load speed should improve  

---

## 📊 Performance Testing

After clearing cache, test performance:

1. **Google PageSpeed Insights**: https://pagespeed.web.dev/
2. **GTmetrix**: https://gtmetrix.com/
3. Check that images show as WebP in the report

---

## ⚡ IMPORTANT NOTES

1. **Don't run `composer update`** on live server (as discussed earlier)
2. **Take a backup** before making changes
3. **Test in incognito mode** to verify cache is cleared
4. **Old visitors** may still see PNG for up to 1 year unless they clear their cache
5. **New visitors** will see WebP immediately after you clear server caches

---

## 🆘 TROUBLESHOOTING

### Problem: "Permission denied" errors
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Problem: WebP command doesn't exist
```bash
# Check available artisan commands
php artisan list | grep webp
```

### Problem: Images still PNG after 24 hours
- Check if your CDN is caching (clear CDN cache)
- Verify `.htaccess` was actually updated on server
- Check Apache is using the `.htaccess` file (`AllowOverride All` in Apache config)

---

## 📞 QUICK REFERENCE

**One-liner to clear everything:**
```bash
php artisan optimize:clear && php artisan webp:regenerate-safely && php artisan config:cache && php artisan route:cache
```

**Check if it's working:**
```bash
curl -I https://yourdomain.com/storage/1/conversions/some-image-large_webp.webp | grep -i "content-type"
# Should show: content-type: image/webp
```
