<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class ExportSqliteToMysqlCommand extends Command
{
    protected $signature = 'db:export-mysql {--file= : Custom output file path}';

    protected $description = 'Export SQLite database to a 100% valid MySQL/MariaDB dump file ready for Hostinger / ServerByte phpMyAdmin import.';

    public function handle(): int
    {
        $startTime = microtime(true);
        $this->info('Starting SQLite to MySQL export...');

        $sqlitePath = database_path('database.sqlite');
        if (! file_exists($sqlitePath)) {
            $this->error("SQLite database file not found at: {$sqlitePath}");

            return Command::FAILURE;
        }

        $outputPath = $this->option('file') ?: database_path('rayka_mysql_dump.sql');
        $outputGzPath = $outputPath . '.gz';

        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all user tables
        $tablesStmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'sessions' ORDER BY name");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        $fp = fopen($outputPath, 'wb');
        if (! $fp) {
            $this->error("Failed to open file for writing: {$outputPath}");

            return Command::FAILURE;
        }

        // Write MySQL header
        fwrite($fp, "-- ========================================================\n");
        fwrite($fp, "-- Rayka Imitation Jewellery - MySQL Database Dump\n");
        fwrite($fp, "-- Converted from SQLite on " . date('Y-m-d H:i:s T') . "\n");
        fwrite($fp, "-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+\n");
        fwrite($fp, "-- Hostinger / ServerByte / cPanel phpMyAdmin Ready\n");
        fwrite($fp, "-- ========================================================\n\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($fp, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($fp, "SET time_zone = \"+00:00\";\n");
        fwrite($fp, "SET NAMES utf8mb4;\n\n");

        $totalRows = 0;
        $totalTables = 0;

        foreach ($tables as $table) {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM \"{$table}\"");
            $rowCount = (int) $countStmt->fetchColumn();

            if ($rowCount === 0) {
                continue;
            }

            $totalTables++;
            $this->info("Exporting table `{$table}` ({$rowCount} rows)...");

            fwrite($fp, "-- --------------------------------------------------------\n");
            fwrite($fp, "-- Table structure and data for table `{$table}` ({$rowCount} rows)\n");
            fwrite($fp, "-- --------------------------------------------------------\n");

            // Write CREATE TABLE DDL
            $ddlStmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='{$table}'");
            $sqliteSql = $ddlStmt->fetchColumn();
            if ($sqliteSql) {
                $mysqlDdl = $this->sqliteToMysqlCreate($sqliteSql);
                fwrite($fp, $mysqlDdl . "\n\n");
            }

            // Disable keys for faster import
            fwrite($fp, "/*!40000 ALTER TABLE `{$table}` DISABLE KEYS */;\n");

            $batchSize = 250;
            $offset = 0;

            while ($offset < $rowCount) {
                $stmt = $pdo->query("SELECT * FROM \"{$table}\" LIMIT {$batchSize} OFFSET {$offset}");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($rows)) {
                    break;
                }

                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                fwrite($fp, "INSERT INTO `{$table}` ({$colList}) VALUES\n");

                $rowStrings = [];
                foreach ($rows as $row) {
                    $vals = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } elseif (is_numeric($val) && ! is_string($val)) {
                            $vals[] = (string) $val;
                        } else {
                            // Clean escaping for MySQL
                            $escaped = str_replace(
                                ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
                                ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
                                (string) $val
                            );
                            $vals[] = "'{$escaped}'";
                        }
                    }
                    $rowStrings[] = '(' . implode(', ', $vals) . ')';
                }

                fwrite($fp, implode(",\n", $rowStrings) . ";\n");
                $totalRows += count($rows);
                $offset += $batchSize;
            }

            fwrite($fp, "/*!40000 ALTER TABLE `{$table}` ENABLE KEYS */;\n\n");
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($fp, "-- ========================================================\n");
        fwrite($fp, "-- Dump Completed. Total tables: {$totalTables}, Total rows: {$totalRows}\n");
        fwrite($fp, "-- ========================================================\n");
        fclose($fp);

        // Also create a gzip version
        if (extension_loaded('zlib') && function_exists('gzopen')) {
            $data = file_get_contents($outputPath);
            $gz = gzopen($outputGzPath, 'w9');
            if ($gz) {
                gzwrite($gz, $data);
                gzclose($gz);
            }
        }

        // Also copy to storage/app/backups
        $backupDir = storage_path('app/backups');
        if (! file_exists($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }
        @copy($outputPath, $backupDir . '/rayka_mysql_dump.sql');
        if (file_exists($outputGzPath)) {
            @copy($outputGzPath, $backupDir . '/rayka_mysql_dump.sql.gz');
        }

        $duration = round(microtime(true) - $startTime, 2);
        $fileSize = round(filesize($outputPath) / 1024, 2);
        $gzSize = file_exists($outputGzPath) ? round(filesize($outputGzPath) / 1024, 2) : 0;

        $this->info("✅ MySQL export completed successfully in {$duration}s!");
        $this->info("📁 Uncompressed SQL: {$outputPath} ({$fileSize} KB)");
        if ($gzSize > 0) {
            $this->info("📦 Compressed GZ: {$outputGzPath} ({$gzSize} KB)");
        }
        $this->info("📊 Total: {$totalTables} tables, {$totalRows} rows exported.");

        return Command::SUCCESS;
    }

    private function sqliteToMysqlCreate(string $sql): string
    {
        $sql = str_replace('"', '`', $sql);
        $sql = preg_replace('/^CREATE TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $sql);
        $sql = preg_replace('/`id`\s+integer\s+primary\s+key\s+autoincrement(\s+not\s+null)?/i', '`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY', $sql);
        $sql = preg_replace('/\binteger\b/i', 'INT', $sql);
        $sql = preg_replace('/\bvarchar(?!\s*\()/i', 'VARCHAR(255)', $sql);
        $sql = preg_replace('/\bdatetime\b/i', 'DATETIME', $sql);
        $sql = rtrim(trim($sql), ';');
        $sql .= ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        return $sql;
    }
}
