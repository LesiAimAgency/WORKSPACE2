<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class WatermarkImageController extends Controller
{
    /**
     * Serve image with watermark applied dynamically
     */
    public function serve(Request $request, string $path)
    {
        // Extract project code from path (e.g., project-SiVGT/image.jpg)
        $projectCode = null;
        if (preg_match('/^project-([^\/]+)\//', $path, $matches)) {
            $projectCode = $matches[1];
        }

        // Get watermark settings for the specific project
        $watermark = $this->getProjectWatermarkSettings($projectCode);

        // Debug: Log watermark settings
        \Log::info('Watermark settings:', [
            'project_code' => $projectCode,
            'raw_settings' => $watermark,
            'type' => gettype($watermark),
            'path' => $path,
        ]);

        // Handle different setting formats
        $enabled = false;
        if (is_array($watermark)) {
            $enabled = filter_var($watermark['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        \Log::info('Watermark enabled check:', ['enabled' => $enabled, 'enabled_raw' => $watermark['enabled'] ?? 'not set']);

        // Build full path - route /media/{path} receives path without 'media/' prefix
        // File is stored at storage/app/public/media/project-xxx/image.jpg
        $fullPath = storage_path("app/public/media/{$path}");

        // Check if file exists
        if (! file_exists($fullPath)) {
            \Log::warning("Watermark: File not found - {$fullPath}");
            abort(404, 'Image not found');
        }

        // Get file info
        $mimeType = mime_content_type($fullPath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Only process image files
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! \in_array($mimeType, $allowedTypes)) {
            return response()->file($fullPath);
        }

        // If watermark is disabled, return original image
        if (! $enabled) {
            \Log::info('Watermark disabled, returning original image');

            return response()->file($fullPath);
        }

        \Log::info('Watermark ENABLED, applying watermark...');

        try {
            // Create image manager with GD driver
            $manager = new ImageManager(new Driver);
            $image = $manager->read($fullPath);

            // Get watermark type: 'text' or 'image'
            $watermarkType = $watermark['type'] ?? 'text';

            if ($watermarkType === 'text') {
                $this->applyTextWatermark($image, $watermark);
            } else {
                $this->applyImageWatermark($image, $watermark);
            }

            // Encode based on original format
            $encoded = match ($extension) {
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                'webp' => $image->toWebp(quality: 90),
                default => $image->toJpeg(quality: 90),
            };

            return response($encoded)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('X-Watermarked', 'true');

        } catch (\Exception $e) {
            \Log::error('Watermark error: '.$e->getMessage());

            return response()->file($fullPath);
        }
    }

    /**
     * Apply text watermark using Intervention Image v3
     */
    protected function applyTextWatermark($image, array $watermark): void
    {
        $text = $watermark['text'] ?? config('app.name', 'Watermark');
        $fontSize = (int) ($watermark['font_size'] ?? 24);
        $fontColor = $watermark['font_color'] ?? 'rgba(255, 255, 255, 0.5)';
        $position = $watermark['position'] ?? 'bottom-right';
        $offsetX = (int) ($watermark['offset_x'] ?? 20);
        $offsetY = (int) ($watermark['offset_y'] ?? 20);
        $angle = (int) ($watermark['angle'] ?? 0);
        $fontFile = $watermark['font_file'] ?? null;

        // Calculate position
        $imageWidth = $image->width();
        $imageHeight = $image->height();

        [$posX, $posY, $align, $valign] = $this->calculateTextPosition(
            $position,
            $imageWidth,
            $imageHeight,
            $offsetX,
            $offsetY
        );

        // Apply text watermark
        $image->text($text, $posX, $posY, function (FontFactory $font) use ($fontSize, $fontColor, $fontFile, $angle, $align, $valign) {
            // Set font file if provided, otherwise use default
            if ($fontFile && file_exists(public_path($fontFile))) {
                $font->filename(public_path($fontFile));
            } else {
                // Use a default font path
                $defaultFont = public_path('fonts/arial.ttf');
                if (file_exists($defaultFont)) {
                    $font->filename($defaultFont);
                }
            }

            $font->size($fontSize);
            $font->color($fontColor);
            $font->align($align);
            $font->valign($valign);
            $font->angle($angle);
        });
    }

    /**
     * Apply image watermark
     */
    protected function applyImageWatermark($image, array $watermark): void
    {
        $watermarkImage = $watermark['image'] ?? '';

        if (empty($watermarkImage)) {
            return;
        }

        // Build watermark full path
        $watermarkPath = $this->resolveWatermarkPath($watermarkImage);

        if (! file_exists($watermarkPath)) {
            \Log::warning("Watermark image not found: {$watermarkPath}");

            return;
        }

        $position = $watermark['position'] ?? 'bottom-right';
        $scale = (int) ($watermark['scale'] ?? 20);
        $opacity = (int) ($watermark['opacity'] ?? 80);
        $offsetX = (int) ($watermark['offset_x'] ?? 10);
        $offsetY = (int) ($watermark['offset_y'] ?? 10);

        $manager = new ImageManager(new Driver);
        $watermarkImg = $manager->read($watermarkPath);

        // Scale watermark
        $imageWidth = $image->width();
        $watermarkWidth = (int) ($imageWidth * $scale / 100);
        $watermarkImg->scale(width: $watermarkWidth);

        // Calculate position
        [$posX, $posY] = $this->calculateImagePosition(
            $position,
            $image->width(),
            $image->height(),
            $watermarkImg->width(),
            $watermarkImg->height(),
            $offsetX,
            $offsetY
        );

        // Place watermark
        $image->place($watermarkImg, 'top-left', $posX, $posY, $opacity);
    }

    /**
     * Calculate text position based on position string
     */
    protected function calculateTextPosition(
        string $position,
        int $imageWidth,
        int $imageHeight,
        int $offsetX,
        int $offsetY
    ): array {
        return match ($position) {
            'top-left' => [$offsetX, $offsetY, 'left', 'top'],
            'top-center' => [(int) ($imageWidth / 2), $offsetY, 'center', 'top'],
            'top-right' => [$imageWidth - $offsetX, $offsetY, 'right', 'top'],
            'center-left' => [$offsetX, (int) ($imageHeight / 2), 'left', 'middle'],
            'center' => [(int) ($imageWidth / 2), (int) ($imageHeight / 2), 'center', 'middle'],
            'center-right' => [$imageWidth - $offsetX, (int) ($imageHeight / 2), 'right', 'middle'],
            'bottom-left' => [$offsetX, $imageHeight - $offsetY, 'left', 'bottom'],
            'bottom-center' => [(int) ($imageWidth / 2), $imageHeight - $offsetY, 'center', 'bottom'],
            'bottom-right' => [$imageWidth - $offsetX, $imageHeight - $offsetY, 'right', 'bottom'],
            default => [$imageWidth - $offsetX, $imageHeight - $offsetY, 'right', 'bottom'],
        };
    }

    /**
     * Calculate image position based on position string
     */
    protected function calculateImagePosition(
        string $position,
        int $imageWidth,
        int $imageHeight,
        int $wmWidth,
        int $wmHeight,
        int $offsetX,
        int $offsetY
    ): array {
        return match ($position) {
            'top-left' => [$offsetX, $offsetY],
            'top-center' => [(int) (($imageWidth - $wmWidth) / 2), $offsetY],
            'top-right' => [$imageWidth - $wmWidth - $offsetX, $offsetY],
            'center-left' => [$offsetX, (int) (($imageHeight - $wmHeight) / 2)],
            'center' => [(int) (($imageWidth - $wmWidth) / 2), (int) (($imageHeight - $wmHeight) / 2)],
            'center-right' => [$imageWidth - $wmWidth - $offsetX, (int) (($imageHeight - $wmHeight) / 2)],
            'bottom-left' => [$offsetX, $imageHeight - $wmHeight - $offsetY],
            'bottom-center' => [(int) (($imageWidth - $wmWidth) / 2), $imageHeight - $wmHeight - $offsetY],
            'bottom-right' => [$imageWidth - $wmWidth - $offsetX, $imageHeight - $wmHeight - $offsetY],
            default => [$imageWidth - $wmWidth - $offsetX, $imageHeight - $wmHeight - $offsetY],
        };
    }

    /**
     * Resolve watermark image path
     */
    protected function resolveWatermarkPath(string $watermarkImage): string
    {
        if (! str_starts_with($watermarkImage, '/')) {
            $watermarkImage = "/{$watermarkImage}";
        }

        if (str_starts_with($watermarkImage, '/storage/')) {
            return storage_path('app/public/'.substr($watermarkImage, 9));
        }

        return public_path(ltrim($watermarkImage, '/'));
    }

    /**
     * Get watermark settings for a specific project
     */
    protected function getProjectWatermarkSettings(?string $projectCode): array
    {
        if (! $projectCode) {
            return setting('watermark', []);
        }

        // Find project by code
        $project = \DB::table('projects')->where('code', $projectCode)->first();

        if (! $project) {
            \Log::warning("Watermark: Project not found - {$projectCode}");

            return [];
        }

        // Get watermark settings from project-specific settings
        $watermarkSetting = \DB::table('settings')
            ->where('key', 'watermark')
            ->where('project_id', $project->id)
            ->first();

        if (! $watermarkSetting) {
            \Log::info("Watermark: No settings found for project {$projectCode}");

            return [];
        }

        $payload = json_decode($watermarkSetting->payload, true);

        \Log::info("Watermark: Loaded settings for project {$projectCode}", ['payload' => $payload]);

        return $payload ?? [];
    }
}
