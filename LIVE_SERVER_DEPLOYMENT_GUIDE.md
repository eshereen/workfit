# Live Server Deployment Guide - CSRF Fix

## Issue
"Page expired" errors when clicking "View Options" button on category pages (e.g., `/categories/women`) on live server only.

## Root Cause
The live server has different caching, session, and CSRF token handling compared to local development.

## Files Modified

### 1. ⚠️ **CRITICAL: Upload New .htaccess** ⚠️
Replace your current `.htaccess` with the updated version that:
- Excludes Livewire requests from LiteSpeed cache
- Properly handles X-CSRF-TOKEN and X-XSRF-TOKEN headers
- Prevents caching of CSRF-sensitive content
- Adds proper cache control headers

### 2. Environment Configuration (.env)
Add these variables to your production `.env` file:

```env
# Session Configuration (Critical for CSRF)
SESSION_DRIVER=database
SESSION_LIFETIME=180
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.yourdomain.com  # Replace with your domain
SESSION_SECURE_COOKIE=true      # For HTTPS sites
SESSION_SAME_SITE=lax
SESSION_HTTP_ONLY=true

# Logging for debugging
LOG_LEVEL=info
```

### 3. Clear All Caches on Live Server
Run these commands on your live server:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan session:table  # If using database sessions
php artisan migrate         # To create session table if needed
```

### 4. Session Storage
If you're using `SESSION_DRIVER=database`, ensure the sessions table exists:

```bash
php artisan session:table
php artisan migrate
```

### 5. LiteSpeed Cache Configuration
If using LiteSpeed, ensure these paths are excluded from cache:
- `/livewire/*`
- Any POST requests
- Requests with X-Livewire headers
- Requests with CSRF tokens

## Testing Steps

1. **Upload all modified files** to your live server
2. **Update .env** with correct domain settings
3. **Clear all caches** (commands above)
4. **Test the fix**:
   - Visit `/categories/women`
   - Click on a product's "View Options" button
   - Should work without "page expired" error

## Debugging on Live Server

The updated code now logs detailed information when CSRF errors occur. Check your Laravel logs for:

```
CategoryProducts: openVariantModal called
LivewireCSRFMiddleware: Processing request
```

## Domain-Specific Issues

### Subdomain Problems
If your site uses www, ensure SESSION_DOMAIN is set correctly:
```env
# For www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com

# For subdomain.yourdomain.com  
SESSION_DOMAIN=.yourdomain.com
```

### HTTPS Requirements
For HTTPS sites (recommended):
```env
SESSION_SECURE_COOKIE=true
APP_URL=https://yourdomain.com
```

## Common Live Server Issues & Solutions

### 1. LiteSpeed Caching CSRF Tokens
**Problem**: Cache serving stale CSRF tokens  
**Solution**: Updated .htaccess excludes Livewire requests from cache

### 2. Session Domain Mismatch
**Problem**: Sessions not persisting across requests  
**Solution**: Set correct SESSION_DOMAIN in .env

### 3. Missing X-CSRF-TOKEN Headers
**Problem**: Server not receiving CSRF headers  
**Solution**: Updated .htaccess properly handles both X-CSRF-TOKEN and X-XSRF-TOKEN

### 4. Browser Cache Issues
**Problem**: Browser caching old CSRF tokens  
**Solution**: Updated cache headers prevent caching of sensitive content

## Emergency Rollback

If issues persist, you can temporarily disable CSRF for Livewire routes by adding to `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'livewire/*',  // Temporary - remove after fixing
];
```

**⚠️ WARNING**: Only use this temporarily for testing!

## Verification Checklist

- [ ] New .htaccess uploaded
- [ ] .env updated with correct domain
- [ ] All caches cleared
- [ ] Session table exists (if using database sessions)
- [ ] HTTPS configured properly
- [ ] "View Options" button works on category pages
- [ ] No "page expired" errors in browser console
- [ ] Laravel logs show successful requests

## Support

If issues persist after following this guide:
1. Check Laravel logs for specific error messages
2. Verify browser network tab shows proper CSRF headers
3. Confirm session cookies are being set with correct domain
4. Test with browser developer tools to inspect request/response headers
