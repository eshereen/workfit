<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class DebugWebPUrls extends Command
{
    protected $signature = 'debug:webp-urls {--limit=5}';
    protected $description = 'Debug WebP URL generation for products';

    public function handle()
    {
        $limit = $this->option('limit');
        $products = Product::with('media')->limit($limit)->get();
        
        $this->info("Checking {$limit} products for WebP URLs...\n");
        
        foreach ($products as $product) {
            $this->line("Product: {$product->name} (ID: {$product->id})");
            
            $media = $product->getFirstMedia('main_image');
            
            if (!$media) {
                $this->warn("  ❌ No media found");
                continue;
            }
            
            $this->line("  Media ID: {$media->id}");
            $this->line("  Original: {$media->file_name}");
            
            // Check if conversion exists in metadata
            $hasConversion = $media->hasGeneratedConversion('medium_webp');
            $this->line("  Has medium_webp conversion: " . ($hasConversion ? '✅ YES' : '❌ NO'));
            
            // Get URL
            $webpUrl = $product->getFirstMediaUrl('main_image', 'medium_webp');
            $originalUrl = $product->getFirstMediaUrl('main_image');
            
            $this->line("  WebP URL: {$webpUrl}");
            $this->line("  Original URL: {$originalUrl}");
            
            if ($webpUrl === $originalUrl) {
                $this->error("  ⚠️  WebP URL same as original - conversion not being used!");
            } else {
                $this->info("  ✅ Different URLs - WebP is active");
            }
            
            $this->newLine();
        }
    }
}
