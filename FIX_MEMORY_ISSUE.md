# Fix Memory Issue After Composer Install

The `dompdf/dompdf` package was successfully installed, but the post-install scripts failed due to memory limits.

## Solution: Run these commands on your live server

### Option 1: Run composer dump-autoload with increased memory (Recommended)
```bash
php -d memory_limit=512M /opt/cpanel/composer/bin/composer dump-autoload --no-interaction
php -d memory_limit=512M artisan package:discover --ansi
php -d memory_limit=512M artisan filament:upgrade
```

### Option 2: If Option 1 doesn't work, increase PHP memory limit temporarily
```bash
# Edit php.ini or create/update .user.ini in your project root
echo "memory_limit = 512M" >> .user.ini

# Then run:
composer dump-autoload --no-interaction
php artisan package:discover --ansi
php artisan filament:upgrade
```

### Option 3: Skip scripts and run manually (if above don't work)
```bash
# Skip scripts during install (already done, but for future reference)
composer require dompdf/dompdf --no-interaction --no-scripts

# Then run scripts manually with more memory:
php -d memory_limit=512M artisan package:discover --ansi
php -d memory_limit=512M artisan filament:upgrade
```

## Verify Installation
After running the commands, verify dompdf is installed:
```bash
composer show dompdf/dompdf
```

The export functionality should work once the autoload files are regenerated.

