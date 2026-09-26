<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupStorageCommand extends Command
{
    protected $signature = 'rayka:setup-storage';

    protected $description = 'Set up external upload and backup folders, clean redundant nested uploads, ensure permissions and create security directives for Hostinger / ServerByte hosting.';

    public function handle(): int
    {
        $this->info('Configuring Rayka storage and backup directories...');

        // 1. Local / Public upload directories
        $subDirs = [
            'uploads/products',
            'uploads/categories',
            'uploads/banners',
            'uploads/payments',
            'uploads/documents',
            'uploads/settings',
            'uploads/qr',
        ];

        foreach ($subDirs as $subDir) {
            $dir = public_path($subDir);
            if (! file_exists($dir)) {
                @mkdir($dir, 0777, true);
                $this->line("  ✓ Created public directory: {$subDir}");
            } else {
                @chmod($dir, 0777);
                $this->line("  ✓ Verified public directory: {$subDir}");
            }
        }

        // 2. Storage backup directory
        $storageBackupDir = storage_path('app/backups');
        if (! file_exists($storageBackupDir)) {
            @mkdir($storageBackupDir, 0755, true);
            $this->line('  ✓ Created storage backup directory: storage/app/backups');
        }

        // 3. External root folders (Hostinger / ServerByte pattern: alongside or inside web root)
        $externalUploadDirs = array_unique(array_filter([
            dirname(base_path()) . DIRECTORY_SEPARATOR . 'rayka_uploads',
            base_path('..' . DIRECTORY_SEPARATOR . 'rayka_uploads'),
            base_path('rayka_uploads'),
            dirname(public_path()) . DIRECTORY_SEPARATOR . 'rayka_uploads',
            isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'rayka_uploads' : null,
        ]));

        foreach ($externalUploadDirs as $extDir) {
            if (! file_exists($extDir)) {
                @mkdir($extDir, 0777, true);
                if (is_dir($extDir)) {
                    $this->info("  ✓ Created external uploads directory: {$extDir}");
                }
            }
        }

        $externalBackupDirs = array_unique(array_filter([
            dirname(base_path()) . DIRECTORY_SEPARATOR . 'backups',
            base_path('..' . DIRECTORY_SEPARATOR . 'backups'),
            base_path('backups'),
            dirname(public_path()) . DIRECTORY_SEPARATOR . 'backups',
            isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'backups' : null,
        ]));

        foreach ($externalBackupDirs as $extBkp) {
            if (! file_exists($extBkp)) {
                @mkdir($extBkp, 0755, true);
                if (is_dir($extBkp)) {
                    $this->info("  ✓ Created external backups directory: {$extBkp}");
                }
            }
        }

        // Sync existing files from public/uploads directly to external rayka_uploads & remove redundant nested uploads/
        $this->syncUploadsToExternal();

        // 4. Security .htaccess inside uploads directory (prevent PHP execution, allow images/PDFs)
        $htaccessPath = public_path('uploads/.htaccess');
        if (! file_exists($htaccessPath)) {
            $htaccessContent = "# Rayka Secure Uploads Directives\n"
                ."<FilesMatch \"\\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|exe)$\">\n"
                ."    Require all denied\n"
                ."</FilesMatch>\n"
                ."<IfModule mod_expires.c>\n"
                ."    ExpiresActive On\n"
                ."    ExpiresDefault \"access plus 1 month\"\n"
                ."</IfModule>\n";
            @file_put_contents($htaccessPath, $htaccessContent);
            $this->line('  ✓ Placed security .htaccess in public/uploads/');
        }

        // 5. Ensure storage link
        if (! file_exists(public_path('storage'))) {
            try {
                $this->call('storage:link');
            } catch (\Throwable $e) {
                $this->warn('  ! Storage link note: '.$e->getMessage());
            }
        }

        $this->info('✅ Rayka storage and backup directories are ready for Hostinger / ServerByte deployment!');

        return Command::SUCCESS;
    }

    private function cleanupNestedUploads(): void
    {
        $targets = [
            base_path('../rayka_uploads'),
            base_path('rayka_uploads'),
        ];

        foreach ($targets as $targetDir) {
            $nested = $targetDir.DIRECTORY_SEPARATOR.'uploads';
            if (is_dir($nested)) {
                try {
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($nested, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::SELF_FIRST
                    );

                    foreach ($iterator as $item) {
                        $subPath = substr($item->getPathname(), strlen($nested));
                        $destination = $targetDir.$subPath;

                        if ($item->isDir()) {
                            if (! file_exists($destination)) {
                                @mkdir($destination, 0777, true);
                            }
                        } else {
                            $parentDir = dirname($destination);
                            if (! file_exists($parentDir)) {
                                @mkdir($parentDir, 0777, true);
                            }
                            @copy($item->getPathname(), $destination);
                            @unlink($item->getPathname());
                        }
                    }

                    // Remove empty subdirectories inside nested uploads
                    $dirs = array_diff(scandir($nested) ?: [], ['.', '..']);
                    foreach ($dirs as $d) {
                        $sub = $nested.DIRECTORY_SEPARATOR.$d;
                        if (is_dir($sub)) {
                            @rmdir($sub);
                        }
                    }
                    @rmdir($nested);
                    $this->line("  ✓ Flattened and removed redundant nested directory: {$nested}");
                } catch (\Throwable $e) {
                    // Silently ignore if cannot rmdir
                }
            }
        }
    }

    private function syncUploadsToExternal(): void
    {
        $uploadsDir = public_path('uploads');
        if (! is_dir($uploadsDir)) {
            return;
        }

        // First clean up any accidental nested uploads folder
        $this->cleanupNestedUploads();

        $targets = array_unique(array_filter([
            dirname(base_path()) . DIRECTORY_SEPARATOR . 'rayka_uploads',
            base_path('..' . DIRECTORY_SEPARATOR . 'rayka_uploads'),
            base_path('rayka_uploads'),
            dirname(public_path()) . DIRECTORY_SEPARATOR . 'rayka_uploads',
            isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'rayka_uploads' : null,
        ]));

        foreach ($targets as $targetDir) {
            if (! is_dir($targetDir)) {
                continue;
            }

            try {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($uploadsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                $syncedCount = 0;
                foreach ($iterator as $item) {
                    $subPath = substr($item->getPathname(), strlen($uploadsDir));
                    $targetPath = $targetDir.$subPath;

                    if ($item->isDir()) {
                        if (! file_exists($targetPath)) {
                            @mkdir($targetPath, 0777, true);
                        }
                    } else {
                        if (! file_exists($targetPath) || filemtime($item->getPathname()) > filemtime($targetPath)) {
                            $parent = dirname($targetPath);
                            if (! file_exists($parent)) {
                                @mkdir($parent, 0777, true);
                            }
                            @copy($item->getPathname(), $targetPath);
                            $syncedCount++;

                            // If this is a category hero banner, also mirror into banners/ folder
                            $fileName = $item->getFilename();
                            if (str_contains($subPath, 'categories') && str_starts_with($fileName, 'hero')) {
                                $extraBannerPath = $targetDir . DIRECTORY_SEPARATOR . 'banners' . DIRECTORY_SEPARATOR . $fileName;
                                $bannerParent = dirname($extraBannerPath);
                                if (! file_exists($bannerParent)) {
                                    @mkdir($bannerParent, 0777, true);
                                }
                                @copy($item->getPathname(), $extraBannerPath);
                            }

                            // If this is a QR code, also mirror into settings/ folder
                            if (str_contains($subPath, 'qr') || str_starts_with($fileName, 'upi_qr')) {
                                $extraSettingsPath = $targetDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . $fileName;
                                $settingsParent = dirname($extraSettingsPath);
                                if (! file_exists($settingsParent)) {
                                    @mkdir($settingsParent, 0777, true);
                                }
                                @copy($item->getPathname(), $extraSettingsPath);
                            }
                        }
                    }
                }
                if ($syncedCount > 0) {
                    $this->line("  ✓ Mirrored {$syncedCount} uploaded files directly to: {$targetDir}");
                }
            } catch (\Throwable $e) {
                // Silently skip if recursion fails
            }
        }
    }
}
