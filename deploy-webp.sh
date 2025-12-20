#!/bin/bash
# WebP Deployment Checklist for Production

echo "======================================"
echo "WebP Image Optimization Deployment"
echo "======================================"
echo ""

echo "Step 1: Clear all caches..."
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo ""
echo "Step 2: Re-cache optimized configs..."
php artisan config:cache
php artisan route:cache

echo ""
echo "Step 3: Check if WebP conversions exist..."
php artisan debug:webp-urls --limit=3

echo ""
echo "======================================"
echo "✅ Deployment Complete!"
echo "======================================"
echo ""
echo "Next steps:"
echo "1. Visit your site and hard refresh (Ctrl+Shift+R)"
echo "2. Check Network tab - images should end in .webp"
echo "3. If still showing .jpg/.png, check that files exist:"
echo "   ls -la storage/app/public/<media-id>/conversions/"
