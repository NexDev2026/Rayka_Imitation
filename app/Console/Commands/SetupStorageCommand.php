<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupStorageCommand extends Command
{
    protected $signature = 'rayka:setup-storage';

    protected $description = 'Set up external upload and backup folders, ensure permissions and create security directives for Hostinger / ServerByte hosting.';

    public function handle(): int
    {
        $this->info('Configuring Rayka storage and backup directories...');

        // 1. Local / Public upload directories
        $subDirs = [
            'uploads/products',
            'uploads/categories',
            'uploads/banners',
            'uploads/documents',
            'uploads/settings',
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
            $this->line("  ✓ Created storage backup directory: storage/app/backups");
        }

        // 3. External root folders (Hostinger / ServerByte pattern: outside public_html)
        $externalUploadsDir = base_path('../rayka_uploads');
        if (! file_exists($externalUploadsDir)) {
            @mkdir($externalUploadsDir, 0777, true);
            if (is_dir($externalUploadsDir)) {
                $this->info("  ✓ Created external uploads directory: {$externalUploadsDir}");
            }
        }

        $externalBackupDir = base_path('../backups');
        if (! file_exists($externalBackupDir)) {
            @mkdir($externalBackupDir, 0755, true);
            if (is_dir($externalBackupDir)) {
                $this->info("  ✓ Created external backups directory: {$externalBackupDir}");
            }
        }

        // 4. Security .htaccess inside uploads directory (prevent PHP execution, allow images/PDFs)
        $htaccessPath = public_path('uploads/.htaccess');
        if (! file_exists($htaccessPath)) {
            $htaccessContent = "# Rayka Secure Uploads Directives\n"
                . "<FilesMatch \"\\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|exe)$\">\n"
                . "    Require all denied\n"
                . "</FilesMatch>\n"
                . "<IfModule mod_expires.c>\n"
                . "    ExpiresActive On\n"
                . "    ExpiresDefault \"access plus 1 month\"\n"
                . "</IfModule>\n";
            @file_put_contents($htaccessPath, $htaccessContent);
            $this->line("  ✓ Placed security .htaccess in public/uploads/");
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
}
