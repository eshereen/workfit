#!/usr/bin/env php
<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "================================\n";
echo "Media Library Detailed Diagnostic\n";
echo "================================\n\n";

// Check GD
echo "1. Image Processing Libraries:\n";
if (extension_loaded('gd')) {
    echo "   ✓ GD is installed\n";
    $gdInfo = gd_info();
    echo "   - WebP Support: " . ($gdInfo['WebP Support'] ?? false ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ GD is NOT installed\n";
}

if (extension_loaded('imagick')) {
    echo "   ✓ Imagick is installed\n";
    $imagick = new Imagick();
    $formats = $imagick->queryFormats();
    echo "   - WebP Support: " . (in_array('WEBP', $formats) ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ Imagick is NOT installed\n";
}
echo "\n";

// Check configuration
echo "2. Media Library Configuration:\n";
echo "   - IMAGE_DRIVER: " . config('media-library.image_driver') . "\n";
echo "   - DISK: " . config('media-library.disk_name') . "\n";
echo "   - QUEUE_CONVERSIONS: " . (config('media-library.queue_conversions_by_default') ? 'Yes' : 'No') . "\n";
echo "   - APP_URL: " . config('app.url') . "\n";
echo "\n";

// Check storage
echo "3. Storage Setup:\n";
$publicPath = storage_path('app/public');
$linkPath = public_path('storage');
echo "   - Storage path exists: " . (is_dir($publicPath) ? 'Yes' : 'No') . "\n";
echo "   - Storage writable: " . (is_writable($publicPath) ? 'Yes' : 'No') . "\n";
echo "   - Symlink exists: " . (is_link($linkPath) ? 'Yes' : 'No') . "\n";
if (is_link($linkPath)) {
    echo "   - Symlink target: " . readlink($linkPath) . "\n";
}
echo "\n";

// Check media records
echo "4. Media Database Records:\n";
$totalMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::count();
echo "   - Total media: {$totalMedia}\n";

if ($totalMedia > 0) {
    $productMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('model_type', 'App\Models\Product')
        ->where('collection_name', 'main_image')
        ->first();
    
    if ($productMedia) {
        echo "\n5. Sample Product Image Analysis:\n";
        echo "   - Media ID: {$productMedia->id}\n";
        echo "   - File: {$productMedia->file_name}\n";
        echo "   - Size: " . number_format($productMedia->size / 1024, 2) . " KB\n";
        
        $originalPath = $productMedia->getPath();
        echo "   - Original path: {$originalPath}\n";
        echo "   - Original exists: " . (file_exists($originalPath) ? 'Yes ✓' : 'No ✗') . "\n";
        
        echo "\n   Conversions:\n";
        $conversions = ['thumb_webp', 'medium_webp', 'large_webp'];
        foreach ($conversions as $conversion) {
            $has = $productMedia->hasGeneratedConversion($conversion);
            echo "   - {$conversion}: " . ($has ? '✓ EXISTS' : '✗ MISSING') . "\n";
            
            if ($has) {
                $path = $productMedia->getPath($conversion);
                $exists = file_exists($path);
                echo "     File: " . basename($path) . "\n";
                echo "     Exists: " . ($exists ? 'Yes' : 'No') . "\n";
                if ($exists) {
                    echo "     Size: " . number_format(filesize($path) / 1024, 2) . " KB\n";
                }
                echo "     URL: " . $productMedia->getUrl($conversion) . "\n";
            }
        }
    }
}
echo "\n";

// Check conversion files on disk
echo "6. Checking Files on Disk:\n";
$conversionsPath = storage_path('app/public');
$webpFiles = glob($conversionsPath . '/**/conversions/*.webp', GLOB_BRACE);
$jpgFiles = glob($conversionsPath . '/**/*.jpg', GLOB_BRACE);
$pngFiles = glob($conversionsPath . '/**/*.png', GLOB_BRACE);

echo "   - WebP conversions: " . count($webpFiles) . " files\n";
echo "   - JPG originals: " . count($jpgFiles) . " files\n";
echo "   - PNG originals: " . count($pngFiles) . " files\n";

if (count($webpFiles) > 0) {
    echo "\n   Sample WebP files:\n";
    foreach (array_slice($webpFiles, 0, 3) as $file) {
        $relativePath = str_replace($conversionsPath . '/', '', $file);
        echo "   - {$relativePath}\n";
    }
}
echo "\n";

echo "================================\n";
echo "Diagnostic Complete!\n";
echo "================================\n";
