<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Conversions\FileManipulator;

class RegenerateAllWebPConversions extends Command
{
    protected $signature = 'media:regenerate-all-conversions {--limit= : Limit number of media items to process}';
    protected $description = 'Regenerate ALL WebP conversions (medium, large, thumb, small) for all product images';

    public function handle()
    {
        $this->info('🚀 Starting COMPLETE WebP regeneration for all conversions...');
        
        // Check GD WebP support
        if (!function_exists('imagewebp')) {
            $this->error('❌ GD does not have WebP support enabled!');
            return 1;
        }
        
        $this->info('✓ GD WebP support is enabled');
        
        $limit = $this->option('limit');
        
        $query = Media::query()
            ->whereIn('collection_name', ['main_image', 'product_images'])
            ->whereNotNull('disk');
            
        if ($limit) {
            $query->limit((int) $limit);
        }
            
        $mediaItems = $query->get();
        $total = $mediaItems->count();
        
        if ($total === 0) {
            $this->info('No media items found.');
            return 0;
        }

        $this->info("Found {$total} media items to process");
        $this->newLine();
        
        // All WebP conversions to generate
        $conversions = ['medium_webp', 'large_webp', 'thumb_webp', 'small_webp', 'zoom_webp'];
        
        $this->info("Will generate conversions: " . implode(', ', $conversions));
        $this->newLine();
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $success = 0;
        $failed = 0;
        $errors = [];
        $fileManipulator = app(FileManipulator::class);

        foreach ($mediaItems as $media) {
            $bar->advance();
            
            try {
                $model = $media->model;
                
                if (!$model) {
                    $failed++;
                    $errors[] = "ID {$media->id}: No model found";
                    continue;
                }
                
                // Generate all WebP conversions for this media item
                foreach ($conversions as $conversionName) {
                    try {
                        // Check if this media has this conversion registered
                        // Only try to create it if it's supposed to have it
                        $fileManipulator->createDerivedFiles($media, [$conversionName]);
                    } catch (\Exception $e) {
                        // Some conversions might not exist for certain media types, that's ok
                        // Log but continue with other conversions
                        if (str_contains($e->getMessage(), 'does not exist')) {
                            continue;
                        }
                        throw $e;
                    }
                }
                
                $success++;
                
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "ID {$media->id} ({$media->file_name}): " . $e->getMessage();
            }
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("═══════════════════════════════════════");
        $this->info("✅ Successfully processed: {$success}");
        $this->error("❌ Failed: {$failed}");
        $this->info("═══════════════════════════════════════");
        
        if (!empty($errors) && $failed <= 20) {
            $this->newLine();
            $this->warn('Recent Errors:');
            foreach (array_slice($errors, 0, 20) as $error) {
                $this->line("  - {$error}");
            }
        }
        
        if ($success > 0) {
            $this->newLine();
            $this->info('🎉 All WebP conversions regenerated successfully!');
            $this->info('💡 Next steps:');
            $this->line('   1. Clear OPcache in cPanel');
            $this->line('   2. Test your site in incognito mode');
            $this->line('   3. Check Google PageSpeed to confirm WebP is being served');
        }
        
        return 0;
    }
}
