#!/bin/bash

echo "================================"
echo "Media Library Diagnostics"
echo "================================"
echo ""

# Check if storage link exists
echo "1. Checking storage symlink..."
if [ -L "public/storage" ]; then
    echo "✓ Storage link exists"
    ls -la public/storage
else
    echo "✗ Storage link does NOT exist"
    echo "  Run: php artisan storage:link"
fi
echo ""

# Check storage directory permissions
echo "2. Checking storage permissions..."
ls -la storage/app/
echo ""

# Check if media files exist
echo "3. Checking media files..."
find storage/app/public -type f -name "*.jpg" -o -name "*.png" -o -name "*.webp" | head -10
echo ""

# Check database for media records
echo "4. Checking media database records..."
php artisan tinker --execute="echo 'Total media: ' . \App\Models\Product::first()?->media?->count() ?? 0;"
echo ""

# Check if conversions exist
echo "5. Checking for WebP conversions..."
find storage/app/public -type f -name "*.webp" | wc -l
echo "WebP files found"
echo ""

# Test URL generation
echo "6. Testing media URL generation..."
php artisan tinker --execute="
\$product = \App\Models\Product::with('media')->first();
if (\$product && \$product->media->count() > 0) {
    echo 'Original: ' . \$product->getFirstMediaUrl('main_image') . PHP_EOL;
    echo 'WebP: ' . \$product->getFirstMediaUrl('main_image', 'medium_webp') . PHP_EOL;
} else {
    echo 'No products with media found' . PHP_EOL;
}
"
echo ""

# Check APP_URL
echo "7. Checking APP_URL configuration..."
grep "APP_URL" .env
echo ""

echo "================================"
echo "Diagnostic complete!"
echo "================================"
