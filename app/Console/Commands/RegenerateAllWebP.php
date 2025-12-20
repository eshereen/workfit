<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegenerateAllWebP extends Command
{
    protected $signature = 'media:regenerate-all-webp {--batch=50}';
    protected $description = 'Regenerate ALL WebP conversions in batches';

    public function handle()
    {
        $batchSize = (int) $this->option('batch');
        
        $this->info('🚀 Starting BATCH WebP regeneration...');
        
        // Check GD WebP support
        if (!function_exists('imagewebp')) {
            $this->error('❌ GD does not have WebP support enabled!');
            return 1;
        }
        
        $this->info('✓ GD WebP support is enabled');
        
        $totalMedia = Media::query()
            ->whereIn('collection_name', ['main_image', 'product_images'])
            ->whereNotNull('disk')
            ->count();
            
        if ($totalMedia === 0) {
            $this->info('No media items found.');
            return 0;
        }

        $this->info("Found {$totalMedia} media items");
        $this->info("Processing in batches of {$batchSize}...");
        $this->newLine();
        
        $totalSuccess = 0;
        $totalFailed = 0;
        $processed = 0;
        
        while ($processed < $totalMedia) {
            $batch = Media::query()
                ->whereIn('collection_name', ['main_image', 'product_images'])
                ->whereNotNull('disk')
                ->skip($processed)
                ->take($batchSize)
                ->get();
                
            if ($batch->isEmpty()) {
                break;
            }
            
            $this->info("📦 Batch " . (floor($processed / $batchSize) + 1) . " ({$batch->count()} items)");
            $bar = $this->output->createProgressBar($batch->count());
            
            foreach ($batch as $item) {
                $bar->advance();
                
                try {
                    $model = $item->model;
                    
                    if (!$model) {
                        $totalFailed++;
                        continue;
                    }
                    
                    // Regenerate WebP conversions
                    $conversionNames = ['medium_webp', 'small_webp', 'thumb_webp', 'large_webp'];
                    
                    foreach ($conversionNames as $conversionName) {
                        try {
                            $item->manipulations = [];
                            $item->save();
                            
                            app(\Spatie\MediaLibrary\Conversions\FileManipulator::class)
                                ->createDerivedFiles($item, [$conversionName]);
                                
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    $totalSuccess++;
                    
                } catch (\Exception $e) {
                    $totalFailed++;
                }
            }
            
            $bar->finish();
            $this->newLine();
            
            $processed += $batch->count();
            
            // Show progress
            $percentComplete = round(($processed / $totalMedia) * 100);
            $this->info("  ✓ Batch complete | Total progress: {$processed}/{$totalMedia} ({$percentComplete}%)");
            $this->newLine();
            
            // Small delay to prevent server overload
            usleep(100000); // 0.1 second
        }
        
        $this->newLine();
        $this->info("═══════════════════════════════════════");
        $this->info("✅ Successfully processed: {$totalSuccess}");
        $this->error("❌ Failed: {$totalFailed}");
        $this->info("═══════════════════════════════════════");
        
        if ($totalSuccess > 0) {
            $this->newLine();
            $this->info('🎉 WebP conversions generated! Your products will now load faster!');
        }
        
        return 0;
    }
}
