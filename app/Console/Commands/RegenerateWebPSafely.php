<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Log;

class RegenerateWebPSafely extends Command
{
    protected $signature = 'media:regenerate-webp-safe {--ids= : Comma separated media IDs} {--only=medium_webp : Conversion name to regenerate} {--force : Regenerate even if conversion already exists}';
    protected $description = 'Safely regenerate WebP conversions one by one with error handling';

    public function handle()
    {
        // Parse command options
        $idsOption   = $this->option('ids');
        $onlyOption  = $this->option('only') ?? 'medium_webp';
        $forceOption = $this->option('force');

        $ids = $idsOption ? array_filter(array_map('trim', explode(',', $idsOption))) : null;
        $conversionName = $onlyOption;

        $this->info('Starting safe WebP regeneration...');
        $this->info("Conversion: {$conversionName}" . ($forceOption ? ' (force)' : ''));

        // Build base query for product media
        $query = Media::where('model_type', 'App\\Models\\Product')
            ->whereIn('collection_name', ['main_image', 'product_images']);
        if ($ids) {
            $query->whereIn('id', $ids);
        }
        $mediaItems = $query->get();

        $total = $mediaItems->count();
        $this->info("Found {$total} media items to process");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0;
        $skipped = 0;
        $failed = [];

        foreach ($mediaItems as $media) {
            try {
                // If conversion already exists and we are NOT forcing, skip it
                if (!$forceOption && $media->hasGeneratedConversion($conversionName)) {
                    $skipped++;
                } else {
                    // Trigger regeneration of all manipulations (required by Spatie)
                    $media->manipulations = $media->manipulations;
                    $media->save();

                    // Regenerate ONLY the requested conversion
                 app(\Spatie\MediaLibrary\Conversions\ConversionCollection::class)
    ->getConversions($media->model->getMediaConversionActions($media))
    ->filter(fn($c) => $c->getName() === $conversionName)
    ->each(function ($c) use ($media) {
        try {
            (new \Spatie\MediaLibrary\Conversions\FileManipulator())
                ->createDerivedFiles($media, [$c]);
        } catch (\Exception $e) {
            Log::warning(
                "Failed conversion {$c->getName()} for media ID {$media->id}: {$e->getMessage()}"
            );
        }
    });

                    $success++;
                }
            } catch (\Exception $e) {
                $failed[] = [
                    'id'    => $media->id,
                    'file'  => $media->file_name,
                    'error' => $e->getMessage(),
                ];
                Log::warning("Failed to regenerate WebP for media ID {$media->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Successfully processed: {$success}");
        $this->info("⏭️  Skipped (already exists): {$skipped}");
        $this->warn("❌ Failed: " . count($failed));

        if (count($failed) > 0) {
            $this->newLine();
            $this->warn('Failed items (check logs for details):');
            foreach (array_slice($failed, 0, 10) as $fail) {
                $this->line("  - ID {$fail['id']}: {$fail['file']}");
            }
            if (count($failed) > 10) {
                $this->line('  ... and ' . (count($failed) - 10) . ' more');
            }
        }

        return Command::SUCCESS;
    }
}
