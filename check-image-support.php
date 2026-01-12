<?php

echo "================================\n";
echo "Image Processing Capabilities Check\n";
echo "================================\n\n";

// Check GD
echo "1. GD Library:\n";
if (extension_loaded('gd')) {
    echo "   ✓ GD is installed\n";
    $gdInfo = gd_info();
    echo "   - Version: " . ($gdInfo['GD Version'] ?? 'Unknown') . "\n";
    echo "   - JPEG Support: " . ($gdInfo['JPEG Support'] ? 'Yes' : 'No') . "\n";
    echo "   - PNG Support: " . ($gdInfo['PNG Support'] ? 'Yes' : 'No') . "\n";
    echo "   - WebP Support: " . ($gdInfo['WebP Support'] ?? false ? 'Yes' : 'No') . "\n";
    echo "   - AVIF Support: " . ($gdInfo['AVIF Support'] ?? false ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ GD is NOT installed\n";
}
echo "\n";

// Check Imagick
echo "2. Imagick Library:\n";
if (extension_loaded('imagick')) {
    echo "   ✓ Imagick is installed\n";
    $imagick = new Imagick();
    $formats = $imagick->queryFormats();
    echo "   - JPEG Support: " . (in_array('JPEG', $formats) ? 'Yes' : 'No') . "\n";
    echo "   - PNG Support: " . (in_array('PNG', $formats) ? 'Yes' : 'No') . "\n";
    echo "   - WebP Support: " . (in_array('WEBP', $formats) ? 'Yes' : 'No') . "\n";
    echo "   - AVIF Support: " . (in_array('AVIF', $formats) ? 'Yes' : 'No') . "\n";
} else {
    echo "   ✗ Imagick is NOT installed\n";
}
echo "\n";

// Check current config
echo "3. Current Configuration:\n";
echo "   - IMAGE_DRIVER: " . config('media-library.image_driver', 'gd') . "\n";
echo "   - QUEUE_CONVERSIONS: " . (config('media-library.queue_conversions_by_default') ? 'Yes' : 'No') . "\n";
echo "\n";

// Check storage
echo "4. Storage Check:\n";
$publicPath = storage_path('app/public');
echo "   - Storage path: {$publicPath}\n";
echo "   - Exists: " . (is_dir($publicPath) ? 'Yes' : 'No') . "\n";
echo "   - Writable: " . (is_writable($publicPath) ? 'Yes' : 'No') . "\n";
echo "\n";

// Check media files
echo "5. Media Files:\n";
$totalMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::count();
echo "   - Total media records: {$totalMedia}\n";

if ($totalMedia > 0) {
    $firstMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::first();
    echo "   - First media ID: {$firstMedia->id}\n";
    echo "   - File name: {$firstMedia->file_name}\n";
    echo "   - Collection: {$firstMedia->collection_name}\n";
    echo "   - Model: {$firstMedia->model_type}\n";
    
    // Check if file exists
    $filePath = $firstMedia->getPath();
    echo "   - File path: {$filePath}\n";
    echo "   - File exists: " . (file_exists($filePath) ? 'Yes' : 'No') . "\n";
    
    // Check conversions
    echo "\n6. Checking Conversions for First Media:\n";
    $conversions = ['thumb_webp', 'medium_webp', 'large_webp'];
    foreach ($conversions as $conversion) {
        $hasConversion = $firstMedia->hasGeneratedConversion($conversion);
        echo "   - {$conversion}: " . ($hasConversion ? 'EXISTS' : 'MISSING') . "\n";
        
        if ($hasConversion) {
            $conversionPath = $firstMedia->getPath($conversion);
            echo "     Path: {$conversionPath}\n";
            echo "     Exists: " . (file_exists($conversionPath) ? 'Yes' : 'No') . "\n";
        }
    }
}
echo "\n";

// Try to generate a test conversion
echo "7. Test Conversion Generation:\n";
try {
    if ($totalMedia > 0) {
        $testMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::first();
        echo "   - Testing with media ID: {$testMedia->id}\n";
        
        // Try to regenerate
        $testMedia->manipulations = [];
        $testMedia->save();
        
        // Trigger conversion
        \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::dispatch($testMedia, []);
        
        echo "   ✓ Dispatched conversion job\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "================================\n";
echo "Diagnostic Complete!\n";
echo "================================\n";
