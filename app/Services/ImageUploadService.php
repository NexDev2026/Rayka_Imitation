<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload an image, auto-convert it to optimized WebP, and return the public URL.
     *
     * @param UploadedFile $file The uploaded file instance
     * @param string $folder Relative folder under public_path, e.g. 'uploads/products'
     * @param string $prefix Filename prefix, e.g. 'prod'
     * @param int $maxWidth Max width in pixels (proportional scale down)
     * @param int $maxHeight Max height in pixels (proportional scale down)
     * @param int $quality WebP quality 1-100 (82 is optimal for crisp jewelry detail)
     * @return string Public URL path, e.g. '/uploads/products/prod_123456_abcde.webp'
     */
    public static function uploadAndConvertToWebp(
        UploadedFile $file,
        string $folder = 'uploads/products',
        string $prefix = 'img',
        int $maxWidth = 1400,
        int $maxHeight = 1400,
        int $quality = 82
    ): string {
        $targetDir = public_path($folder);
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();

        // 1. If vector SVG, keep as SVG without rasterizing
        if ($extension === 'svg' || str_contains($mime, 'svg')) {
            $filename = $prefix . '_' . time() . '_' . Str::random(6) . '.svg';
            $file->move($targetDir, $filename);
            self::mirrorToExternalUploads($targetDir . DIRECTORY_SEPARATOR . $filename, $folder, $filename);
            return '/' . trim($folder, '/') . '/' . $filename;
        }

        // 2. Read image content
        $realPath = $file->getRealPath();
        $binary = @file_get_contents($realPath);
        if (!$binary) {
            // Fallback move
            $filename = $prefix . '_' . time() . '_' . Str::random(6) . '.' . $extension;
            $file->move($targetDir, $filename);
            self::mirrorToExternalUploads($targetDir . DIRECTORY_SEPARATOR . $filename, $folder, $filename);
            return '/' . trim($folder, '/') . '/' . $filename;
        }

        $src = @imagecreatefromstring($binary);
        if (!$src || !function_exists('imagewebp')) {
            // Fallback if GD string parser or imagewebp is unavailable
            $filename = $prefix . '_' . time() . '_' . Str::random(6) . '.' . $extension;
            $file->move($targetDir, $filename);
            self::mirrorToExternalUploads($targetDir . DIRECTORY_SEPARATOR . $filename, $folder, $filename);
            return '/' . trim($folder, '/') . '/' . $filename;
        }

        // 3. Fix smartphone camera EXIF orientation if available (JPEG)
        if (function_exists('exif_read_data') && in_array($extension, ['jpg', 'jpeg'])) {
            try {
                $exif = @exif_read_data($realPath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $src = imagerotate($src, 180, 0);
                            break;
                        case 6:
                            $src = imagerotate($src, -90, 0);
                            break;
                        case 8:
                            $src = imagerotate($src, 90, 0);
                            break;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore exif reading failures
            }
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        // 4. Calculate downscaled dimensions preserving exact aspect ratio
        $targetW = $origW;
        $targetH = $origH;

        if ($origW > $maxWidth || $origH > $maxHeight) {
            $ratio = min($maxWidth / $origW, $maxHeight / $origH);
            $targetW = max(1, (int) round($origW * $ratio));
            $targetH = max(1, (int) round($origH * $ratio));
        }

        // 5. Create truecolor canvas with full alpha transparency support
        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $targetW, $targetH, $transparent);
        imagealphablending($dst, true);

        // Resample with anti-aliasing
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        // 6. Save as modern .webp file
        $baseName = $prefix . '_' . time() . '_' . Str::random(6);
        $finalFilename = $baseName . '.webp';
        $destinationPath = $targetDir . DIRECTORY_SEPARATOR . $finalFilename;

        imagewebp($dst, $destinationPath, $quality);

        // Cleanup GD memory handles
        imagedestroy($dst);
        imagedestroy($src);

        // Mirror to external uploads folder for hosting backups & cPanel file manager
        self::mirrorToExternalUploads($destinationPath, $folder, $finalFilename);

        return '/' . trim($folder, '/') . '/' . $finalFilename;
    }

    /**
     * Mirror an uploaded file to external rayka_uploads directories if configured or present.
     */
    public static function mirrorToExternalUploads(string $sourceFilePath, string $subFolder, string $filename): void
    {
        // Strip any leading 'uploads/' so 'uploads/banners' becomes 'banners', directly inside rayka_uploads/banners
        $cleanSubFolder = preg_replace('#^uploads/?#i', '', trim($subFolder, '/'));

        $destinations = [
            base_path('../rayka_uploads' . ($cleanSubFolder !== '' ? '/' . $cleanSubFolder : '')),
            base_path('rayka_uploads' . ($cleanSubFolder !== '' ? '/' . $cleanSubFolder : '')),
        ];

        foreach ($destinations as $dir) {
            try {
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777, true);
                }
                if (is_dir($dir) && file_exists($sourceFilePath)) {
                    @copy($sourceFilePath, $dir . DIRECTORY_SEPARATOR . $filename);
                }
            } catch (\Throwable $e) {
                // Silently continue if external dir cannot be written
            }
        }
    }
}
