#!/bin/bash

echo "================================"
echo "Media Library Fix Script"
echo "================================"
echo ""

# Step 1: Create storage link
echo "Step 1: Creating storage symlink..."
php artisan storage:link
echo "✓ Done"
echo ""

# Step 2: Set proper permissions
echo "Step 2: Setting proper permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R $USER:www-data storage
chown -R $USER:www-data bootstrap/cache
echo "✓ Done"
echo ""

# Step 3: Clear all caches
echo "Step 3: Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo "✓ Done"
echo ""

# Step 4: Optimize
echo "Step 4: Optimizing..."
php artisan config:cache
php artisan route:cache
echo "✓ Done"
echo ""

# Step 5: Regenerate media conversions
echo "Step 5: Regenerating missing media conversions..."
php artisan media-library:regenerate --only-missing --force
echo "✓ Done"
echo ""

echo "================================"
echo "Fix complete!"
echo "Please check your website now."
echo "================================"
