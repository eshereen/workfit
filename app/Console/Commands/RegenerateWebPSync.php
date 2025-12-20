<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Conversions\ConversionCollection;

class RegenerateWebPSync extends Command
{
    protected $signature = 'media:regenerate-webp-sync {--ids=} {--limit=10}';
    protected $description = 'Synchronously regenerate WebP conversions (no queue)';

    public function handle()
    {
        $this->info('Starting SYNCHRONOUS WebP regeneration...');
        
        // Check GD WebP support
        if (!function_exists('imagewebp')) {
            $this->error('❌ GD does not have WebP support enabled!');
            $this->info('Contact your hosting provider to enable GD WebP support.');
            return 1;
        }
        
        $this->info('✓ GD WebP support is enabled');
        
        $limit = (int) $this->option('limit');
        $ids = $this->option('ids');
        
        $query = Media::query()
            ->whereIn('collection_name', ['main_image', 'product_images'])
            ->whereNotNull('disk');
            
        if ($ids) {
            $idArray = explode(',', $ids);
            $query->whereIn('id', $idArray);
        }
        
        $media = $query->limit($limit)->get();
        
        if ($media->isEmpty()) {
            $this->info('No media items found.');
            return 0;
        }

        $this->info("Processing {$media->count()} media items (limit: {$limit})...");
        $bar = $this->output->createProgressBar($media->count());
        
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($media as $item) {
            $bar->advance();
            
            try {
                // Get the model
                $model = $item->model;
                
                if (!$model) {
                    $failed++;
                    $errors[] = "ID {$item->id}: No model found";
                    continue;
                }
                
                // Regenerate all conversions for this media item
                // This uses Spatie's built-in regeneration
                $conversionNames = ['medium_webp', 'small_webp', 'thumb_webp', 'large_webp'];
                
                foreach ($conversionNames as $conversionName) {
                    try {
                        // Generate the conversion
                        $item->manipulations = [];
                        $item->save();
                        
                        // Trigger regeneration
                        app(\Spatie\MediaLibrary\Conversions\FileManipulator::class)
                            ->createDerivedFiles($item, [$conversionName]);
                            
                    } catch (\Exception $e) {
                        // Continue with other conversions even if one fails
                        continue;
                    }
                }
                
                $success++;
                
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "ID {$item->id}: " . $e->getMessage();
            }
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Successfully processed: {$success}");
        $this->error("❌ Failed: {$failed}");
        
        if (!empty($errors) && $failed <= 10) {
            $this->newLine();
            $this->warn('Errors:');
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }
        }
        
        if ($failed > 0) {
            $this->newLine();
            $this->warn('💡 Tip: Run with --limit=5 to test a smaller batch first');
        }
        
        return 0;
    }
}
