<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeBannerImages extends Command
{
    protected $signature = 'banners:optimize-images {--force}';
    protected $description = 'Optimize banner images for better LCP performance';

    public function handle()
    {
        $this->info('🚀 Starting banner image optimization...');
        
        // Check if GD or Imagick is available
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            $this->error('❌ Neither GD nor Imagick extension is installed.');
            $this->info('Please install one: sudo apt-get install php-gd OR sudo apt-get install php-imagick');
            return 1;
        }

        $disk = Storage::disk('public');
        $optimized = 0;
        $skipped = 0;

        // Find all banner images
        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $bannersPath = 'banners';
        
        if (!$disk->exists($bannersPath)) {
            $this->warn('No banners directory found.');
            return 0;
        }

        $files = collect($disk->allFiles($bannersPath))
            ->filter(function ($file) use ($imageExtensions) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                return in_array($ext, $imageExtensions);
            });

        if ($files->isEmpty()) {
            $this->info('No images found to optimize.');
            return 0;
        }

        $this->info("Found {$files->count()} images to process.");
        $bar = $this->output->createProgressBar($files->count());

        foreach ($files as $file) {
            $bar->advance();
            
            $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file);
            
            // Skip if WebP already exists and we're not forcing
            if ($disk->exists($webpPath) && !$this->option('force')) {
                $skipped++;
                continue;
            }

            try {
                $fullPath = $disk->path($file);
                $webpFullPath = $disk->path($webpPath);
                
                // Use GD to convert to WebP
                if (extension_loaded('gd')) {
                    $this->convertToWebPWithGD($fullPath, $webpFullPath);
                } elseif (extension_loaded('imagick')) {
                    $this->convertToWebPWithImagick($fullPath, $webpFullPath);
                }
                
                $optimized++;
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error optimizing {$file}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Optimized: {$optimized} images");
        $this->info("⊘ Skipped: {$skipped} images (already exist)");
        
       return 0;
    }

    private function convertToWebPWithGD($source, $destination)
    {
        $info = getimagesize($source);
        
        if ($info === false) {
            throw new \Exception('Cannot read image info');
        }

        $image = match($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG => imagecreatefrompng($source),
            default => throw new \Exception('Unsupported image type')
        };

        if (!$image) {
            throw new \Exception('Failed to create image resource');
        }

        // Resize if too large
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($width > 1920) {
            $newWidth = 1920;
            $newHeight = (int)(($height / $width) * $newWidth);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Convert to WebP with 80% quality
        imagewebp($image, $destination, 80);
        imagedestroy($image);
    }

    private function convertToWebPWithImagick($source, $destination)
    {
        $image = new \Imagick($source);
        
        // Resize if too large
        if ($image->getImageWidth() > 1920) {
            $image->scaleImage(1920, 0);
        }
        
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality(80);
        $image->writeImage($destination);
        $image->clear();
        $image->destroy();
    }
}
