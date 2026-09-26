<?php

namespace App\Console\Commands;

use App\Mail\DatabaseBackupMail;
use App\Models\StoreSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDO;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup {--no-mail : Skip sending admin email notification}';

    protected $description = 'Perform an automated pure-PHP database backup snapshot, gzip compress it, rotate old backups, and dispatch an admin notification email.';

    public function handle(): int
    {
        $startTime = microtime(true);
        $this->info('Starting automated database backup snapshot...');

        $backupDir = storage_path('app/backups');
        if (! file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        try {
            $pdo = DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
        } catch (\Throwable $e) {
            $this->error('Database connection failed: '.$e->getMessage());
            Log::error('Backup failed: DB connection error: '.$e->getMessage());

            return Command::FAILURE;
        }

        $dateSlug = date('Y-m-d_His');
        $rawSqlFilename = 'rayka_backup_'.$dateSlug.'.sql';
        $rawSqlFilepath = $backupDir.'/'.$rawSqlFilename;

        $fp = @fopen($rawSqlFilepath, 'wb');
        if (! $fp) {
            $this->error('Failed to open backup destination file: '.$rawSqlFilepath);

            return Command::FAILURE;
        }

        $write = function (string $chunk) use ($fp) {
            fwrite($fp, $chunk);
        };

        // Header
        $header = "-- ========================================================\n";
        $header .= "-- Rayka Imitation Jewellery Database Backup\n";
        $header .= '-- Date: '.date('Y-m-d H:i:s T')."\n";
        $header .= '-- Driver: '.$driver."\n";
        $header .= "-- ========================================================\n\n";

        if ($driver === 'mysql') {
            $header .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $header .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n";
            $header .= "SET time_zone = \"+00:00\";\n";
            $header .= "SET NAMES utf8mb4;\n\n";
        }

        $write($header);
        $totalRows = 0;
        $tableNames = [];

        try {
            if ($driver === 'mysql') {
                $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                $tables = $stmt->fetchAll(PDO::FETCH_NUM);
                $tableNames = array_map(fn ($row) => $row[0], $tables);
            } else {
                // SQLite or other fallback
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tables = $stmt->fetchAll(PDO::FETCH_NUM);
                $tableNames = array_map(fn ($row) => $row[0], $tables);
            }

            foreach ($tableNames as $table) {
                $write("-- --------------------------------------------------------\n");
                $write("-- Table structure for `{$table}`\n");
                $write("-- --------------------------------------------------------\n");

                if ($driver === 'mysql') {
                    $write("DROP TABLE IF EXISTS `{$table}`;\n");
                    $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                    $createRow = $createStmt->fetch(PDO::FETCH_NUM);
                    if (! empty($createRow[1])) {
                        $write($createRow[1].";\n\n");
                    }
                } else {
                    $createStmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'");
                    $createSql = $createStmt->fetchColumn();
                    if ($createSql) {
                        $write("DROP TABLE IF EXISTS \"{$table}\";\n");
                        $write($createSql.";\n\n");
                    }
                }

                $tblQuote = $driver === 'mysql' ? "`{$table}`" : "\"{$table}\"";
                $countStmt = $pdo->query("SELECT COUNT(*) FROM {$tblQuote}");
                $rowCount = (int) $countStmt->fetchColumn();

                if ($rowCount === 0) {
                    $write("-- (Table is empty)\n\n");

                    continue;
                }

                $write("-- Dumping data for table `{$table}`\n");
                $batchSize = 300;
                $offset = 0;

                while ($offset < $rowCount) {
                    $dataStmt = $pdo->query("SELECT * FROM {$tblQuote} LIMIT {$batchSize} OFFSET {$offset}");
                    $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        break;
                    }

                    $columns = array_keys($rows[0]);
                    $colList = implode('`, `', $columns);
                    $write("INSERT INTO `{$table}` (`{$colList}`) VALUES\n");

                    $valuesList = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($row as $val) {
                            if ($val === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = $pdo->quote((string) $val);
                            }
                        }
                        $valuesList[] = '('.implode(', ', $rowValues).')';
                    }

                    $write(implode(",\n", $valuesList).";\n");
                    $totalRows += count($rows);
                    $offset += $batchSize;
                }

                $write("\n");
            }

            if ($driver === 'mysql') {
                $write("\nSET FOREIGN_KEY_CHECKS=1;\n");
            }
            $write("-- End of Rayka Database Backup\n");

        } catch (\Throwable $e) {
            $this->error('Error during table dumping: '.$e->getMessage());
            Log::error('Backup dumping error: '.$e->getMessage());
        } finally {
            if (is_resource($fp)) {
                fclose($fp);
            }
        }

        // Verify dump file size > 0
        clearstatcache(true, $rawSqlFilepath);
        $rawSqlSize = file_exists($rawSqlFilepath) ? filesize($rawSqlFilepath) : 0;
        if ($rawSqlSize <= 0) {
            @unlink($rawSqlFilepath);
            $this->error('Backup produced an empty 0-byte file.');

            return Command::FAILURE;
        }

        // 1. Package into standard ZIP archive (native support on Windows/Mac/Linux and accepted by Brevo API)
        $zipFilename = 'rayka_backup_'.$dateSlug.'.zip';
        $zipFilepath = $backupDir.'/'.$zipFilename;
        $zipCreated = false;

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive;
            if ($zip->open($zipFilepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFile($rawSqlFilepath, $rawSqlFilename);
                $zip->close();
                clearstatcache(true, $zipFilepath);
                $zipCreated = file_exists($zipFilepath) && filesize($zipFilepath) > 0;
            }
        }

        // 2. Also create standard GZIP archive if zlib extension is available
        $gzFilename = 'rayka_backup_'.$dateSlug.'.sql.gz';
        $gzFilepath = $backupDir.'/'.$gzFilename;
        if (extension_loaded('zlib') && function_exists('gzopen')) {
            $gzFp = @gzopen($gzFilepath, 'w9');
            $rawFp = @fopen($rawSqlFilepath, 'rb');
            if ($gzFp && $rawFp) {
                while (! feof($rawFp)) {
                    gzwrite($gzFp, fread($rawFp, 1024 * 512));
                }
                fclose($rawFp);
                gzclose($gzFp);
            }
        }

        // Clean up temporary uncompressed raw SQL dump to conserve server disk space
        @unlink($rawSqlFilepath);

        // Determine primary backup artifact
        $filename = $zipCreated ? $zipFilename : (file_exists($gzFilepath) ? $gzFilename : $rawSqlFilename);
        $filepath = $zipCreated ? $zipFilepath : (file_exists($gzFilepath) ? $gzFilepath : $rawSqlFilepath);
        $fileSize = file_exists($filepath) ? filesize($filepath) : 0;

        $readableSize = $this->formatBytes($fileSize);
        $duration = round(microtime(true) - $startTime, 2);

        // Auto-Rotate backups older than 7 days
        $rotatedCount = $this->rotateOldBackups($backupDir, 7);

        // Mirror backup to external root backups folder (e.g. /home/user/backups on Hostinger / ServerByte)
        $externalDirs = array_unique(array_filter([
            dirname(base_path()).DIRECTORY_SEPARATOR.'backups',
            base_path('..'.DIRECTORY_SEPARATOR.'backups'),
            base_path('backups'),
            dirname(public_path()).DIRECTORY_SEPARATOR.'backups',
            isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'backups' : null,
        ]));

        $artifactsToMirror = array_filter([$zipFilepath, $gzFilepath], fn ($p) => file_exists($p));

        foreach ($externalDirs as $dir) {
            try {
                if (! file_exists($dir)) {
                    @mkdir($dir, 0755, true);
                }
                if (is_dir($dir)) {
                    foreach ($artifactsToMirror as $artPath) {
                        $artName = basename($artPath);
                        @copy($artPath, $dir.DIRECTORY_SEPARATOR.$artName);
                    }
                    $this->rotateOldBackups($dir, 7);
                    $this->info("Backup mirrored to: {$dir}/{$filename}");
                }
            } catch (\Throwable $e) {
                // Continue to next destination
            }
        }

        $isAttached = ($zipCreated && $fileSize > 0 && $fileSize <= 8 * 1024 * 1024);

        $result = [
            'status' => 'success',
            'filename' => $filename,
            'filepath' => $filepath,
            'size_bytes' => $fileSize,
            'size_readable' => $readableSize,
            'tables' => count($tableNames),
            'rows' => $totalRows,
            'duration_sec' => $duration,
            'rotated' => $rotatedCount,
            'is_attached' => $isAttached,
        ];

        $this->info("Backup complete: {$filename} ({$readableSize}, {$totalRows} rows) in {$duration}s. Rotated {$rotatedCount} old files.");

        // Dispatch admin notification email to all configured admin email recipients
        if (! $this->option('no-mail')) {
            $recipients = array_values(array_filter(array_unique([
                StoreSetting::get('admin_email'),
                StoreSetting::get('store_email'),
                config('services.brevo.admin_email'),
                env('ADMIN_EMAIL'),
            ])));

            if (empty($recipients)) {
                $recipients = ['nexdevstudio01@gmail.com'];
            }

            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient)->send(new DatabaseBackupMail($result));
                    $this->info("Admin backup notification email successfully sent to {$recipient}.");
                } catch (\Throwable $e) {
                    $this->warn("Dispatch with attachment failed for {$recipient}: ".$e->getMessage().'. Retrying without attachment...');
                    Log::warning("Backup email with attachment failed for {$recipient}: ".$e->getMessage());

                    try {
                        $resultFallback = $result;
                        $resultFallback['skip_attachment'] = true;
                        $resultFallback['is_attached'] = false;
                        Mail::to($recipient)->send(new DatabaseBackupMail($resultFallback));
                        $this->info("Admin backup notification email successfully sent to {$recipient} (without attachment).");
                    } catch (\Throwable $e2) {
                        $this->error("Backup email fallback delivery failed for {$recipient}: ".$e2->getMessage());
                        Log::error("Backup email fallback delivery failed for {$recipient}: ".$e2->getMessage());
                    }
                }
            }
        }

        return Command::SUCCESS;
    }

    private function rotateOldBackups(string $dir, int $days = 7): int
    {
        $deleted = 0;
        $cutoff = time() - ($days * 86400);
        $files = glob($dir.'/rayka_backup_*.*');
        if (! $files) {
            return 0;
        }

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                if (@unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
