# Live Server Debug Steps - CSRF Issue

## 🚨 URGENT: Follow These Steps to Diagnose Your Live Server Issue

### Step 1: Verify Files Were Uploaded Correctly

1. **Check .htaccess file exists and is correct**
   ```bash
   # On your live server, check if file exists:
   ls -la .htaccess
   
   # Check if it contains the LiteSpeed cache exclusions:
   grep -i "livewire\|csrf" .htaccess
   ```

2. **Verify the new middleware exists**
   ```bash
   ls -la app/Http/Middleware/LivewireCSRFMiddleware.php
   ```

### Step 2: Test Debug Endpoints (CRITICAL)

Visit these URLs on your live server to identify the exact issue:

1. **Session Debug**: `https://yourdomain.com/debug/session`
   - Check if session_id changes on each refresh
   - Verify csrf_token is being generated
   - Check session_driver is correct

2. **Middleware Debug**: `https://yourdomain.com/debug/middleware`
   - Verify LivewireCSRFMiddleware is active
   - Check if middleware is registered correctly

3. **CSRF Test**: Make a POST request to `https://yourdomain.com/debug/csrf-test`
   ```javascript
   // Test in browser console:
   fetch('/debug/csrf-test', {
       method: 'POST',
       headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
           'Content-Type': 'application/json'
       }
   }).then(r => r.json()).then(console.log)
   ```

### Step 3: Check Environment Configuration

1. **Verify .env settings**:
   ```env
   # These MUST be set correctly:
   APP_ENV=production
   SESSION_DOMAIN=.yourdomain.com  # Replace with your actual domain
   SESSION_SECURE_COOKIE=true      # If using HTTPS
   SESSION_DRIVER=database         # Recommended for live servers
   CACHE_DRIVER=file              # Or redis if available
   ```

2. **Create sessions table if using database driver**:
   ```bash
   php artisan session:table
   php artisan migrate
   ```

### Step 4: Clear ALL Caches (CRITICAL)

```bash
# Run these commands on your live server:
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# If using OPcache:
php artisan opcache:clear
```

### Step 5: Check Server Logs

1. **Laravel Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for:
   - `LivewireCSRFMiddleware: Processing request`
   - `CategoryProducts: openVariantModal called`
   - Any CSRF-related errors

2. **Server Error Logs**:
   - Check Apache/Nginx error logs
   - Check PHP error logs
   - Look for any middleware-related errors

### Step 6: Test Different Scenarios

1. **Test without cache**:
   - Add `?nocache=1` to your URLs
   - Disable any CDN temporarily

2. **Test with different browsers**:
   - Clear all cookies/storage
   - Try incognito mode
   - Test on mobile vs desktop

### Step 7: Server-Specific Checks

#### If using cPanel/Shared Hosting:
```bash
# Check if mod_rewrite is enabled:
grep -i rewrite .htaccess

# Check if your hosting supports .htaccess modifications
```

#### If using LiteSpeed:
```bash
# Check if LiteSpeed cache is interfering:
# Look for cache headers in browser network tab
# Try adding this to .htaccess temporarily:
# RewriteRule ^(.*)$ - [E=Cache-Control:no-cache]
```

#### If using Cloudflare or CDN:
- Temporarily disable CDN
- Check if "Development Mode" fixes the issue
- Verify cache settings exclude POST requests

### Step 8: Emergency Workarounds

If the issue persists, try these temporary fixes:

1. **Disable CSRF for Livewire (TEMPORARY ONLY)**:
   Create `app/Http/Middleware/VerifyCsrfToken.php`:
   ```php
   <?php
   namespace App\Http\Middleware;
   
   use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
   
   class VerifyCsrfToken extends Middleware
   {
       protected $except = [
           'livewire/*',  // TEMPORARY - REMOVE AFTER FIXING
       ];
   }
   ```

2. **Force session regeneration**:
   Add to `app/Livewire/CategoryProducts.php` mount method:
   ```php
   public function mount($categorySlug = null)
   {
       // Force session start on live server
       if (app()->environment('production')) {
           session()->regenerate();
       }
       
       // ... existing code
   }
   ```

### Step 9: Report Back

Send me the results of:
1. The JSON output from `/debug/session`
2. The JSON output from `/debug/middleware` 
3. Any errors from the CSRF test
4. Your exact domain and hosting setup
5. Results from the Laravel log

### Common Issues & Solutions

| Issue | Symptoms | Solution |
|-------|----------|----------|
| Session domain mismatch | Sessions not persisting | Set `SESSION_DOMAIN=.yourdomain.com` |
| LiteSpeed cache | Works first time, fails after | Update .htaccess to exclude Livewire |
| Missing sessions table | Database errors | Run `php artisan session:table && php artisan migrate` |
| CDN interference | Intermittent failures | Exclude POST requests from CDN cache |
| Wrong middleware | No debug logs | Verify `LivewireCSRFMiddleware` is active |

### 🔍 What to Look For

The debug endpoints will tell us exactly what's wrong:
- ❌ Session ID changing = session storage issue
- ❌ Different CSRF tokens = middleware not working
- ❌ Middleware not found = file upload issue
- ❌ Domain mismatch = configuration issue

**Run these steps and let me know the results!**
