<?php

namespace App\Console\Commands;

use App\Mail\DatabaseBackupMail;
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
        $useGzip = extension_loaded('zlib') && function_exists('gzopen');
        $filename = 'rayka_backup_'.$dateSlug.($useGzip ? '.sql.gz' : '.sql');
        $filepath = $backupDir.'/'.$filename;

        $fp = $useGzip ? @gzopen($filepath, 'w9') : @fopen($filepath, 'wb');
        if (! $fp) {
            $this->error('Failed to open backup destination file: '.$filepath);

            return Command::FAILURE;
        }

        $write = function (string $chunk) use ($fp, $useGzip) {
            if ($useGzip) {
                gzwrite($fp, $chunk);
            } else {
                fwrite($fp, $chunk);
            }
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
            if ($useGzip) {
                gzclose($fp);
            } else {
                fclose($fp);
            }
        }

        // Verify file size > 0
        clearstatcache(true, $filepath);
        $fileSize = file_exists($filepath) ? filesize($filepath) : 0;
        if ($fileSize <= 0) {
            @unlink($filepath);
            $this->error('Backup produced an empty 0-byte file.');

            return Command::FAILURE;
        }

        $readableSize = $this->formatBytes($fileSize);
        $duration = round(microtime(true) - $startTime, 2);

        // Auto-Rotate backups older than 7 days
        $rotatedCount = $this->rotateOldBackups($backupDir, 7);

        // Mirror backup to external root backups folder (e.g. /home/user/backups on Hostinger / ServerByte)
        $externalDirs = array_unique(array_filter([
            dirname(base_path()) . DIRECTORY_SEPARATOR . 'backups',
            base_path('..' . DIRECTORY_SEPARATOR . 'backups'),
            base_path('backups'),
            dirname(public_path()) . DIRECTORY_SEPARATOR . 'backups',
            isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR . 'backups' : null,
        ]));

        foreach ($externalDirs as $dir) {
            try {
                if (! file_exists($dir)) {
                    @mkdir($dir, 0755, true);
                }
                if (is_dir($dir)) {
                    @copy($filepath, $dir . DIRECTORY_SEPARATOR . $filename);
                    if (file_exists($dir . DIRECTORY_SEPARATOR . $filename)) {
                        $this->rotateOldBackups($dir, 7);
                        $this->info("Backup also saved to: {$dir}/{$filename}");
                    }
                }
            } catch (\Throwable $e) {
                // Continue to next destination
            }
        }

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
        ];

        $this->info("Backup complete: {$filename} ({$readableSize}, {$totalRows} rows) in {$duration}s. Rotated {$rotatedCount} old files.");

        // Dispatch admin notification email to all configured admin email recipients
        if (! $this->option('no-mail')) {
            try {
                $recipients = array_values(array_filter(array_unique([
                    \App\Models\StoreSetting::get('admin_email'),
                    \App\Models\StoreSetting::get('store_email'),
                    config('services.brevo.admin_email'),
                    env('ADMIN_EMAIL'),
                ])));

                if (empty($recipients)) {
                    $recipients = ['nexdevstudio01@gmail.com'];
                }

                foreach ($recipients as $recipient) {
                    Mail::to($recipient)->send(new DatabaseBackupMail($result));
                    $this->info("Admin backup notification email successfully sent to {$recipient}.");
                }
            } catch (\Throwable $e) {
                $this->warn('Could not dispatch backup email: '.$e->getMessage());
                Log::warning('Backup email notification failed: '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    private function rotateOldBackups(string $dir, int $days = 7): int
    {
        $deleted = 0;
        $cutoff = time() - ($days * 86400);
        $files = glob($dir.'/rayka_backup_*.sql*');
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
