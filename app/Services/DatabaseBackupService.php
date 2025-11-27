<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseBackupService
{
    protected $backupPath = 'backups/database';
    
    /**
     * Create a database backup
     */
    public function backup(): array
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        
        $filename = 'backup_' . $database . '_' . date('Y-m-d_His') . '.sql';
        $storagePath = storage_path('app/' . $this->backupPath);
        
        // Create directory if not exists
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        
        $filePath = $storagePath . '/' . $filename;
        
        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s > "%s" 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            $filePath
        );
        
        // Execute backup
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
            // Fallback: PHP-based backup
            return $this->phpBackup($filePath, $filename);
        }
        
        $fileSize = filesize($filePath);
        
        Log::info("Database backup created: {$filename}, Size: {$fileSize} bytes");
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $filePath,
            'size' => $this->formatBytes($fileSize),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * PHP-based backup fallback (when mysqldump not available)
     */
    protected function phpBackup(string $filePath, string $filename): array
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $database;
        
        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Get table data
            $rows = DB::table($tableName)->get();
            
            if ($rows->count() > 0) {
                $columns = array_keys((array) $rows->first());
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, (array) $row);
                    
                    $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        file_put_contents($filePath, $sql);
        $fileSize = filesize($filePath);
        
        Log::info("Database backup created (PHP method): {$filename}, Size: {$fileSize} bytes");
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $filePath,
            'size' => $this->formatBytes($fileSize),
            'created_at' => now()->format('Y-m-d H:i:s'),
            'method' => 'php'
        ];
    }
    
    /**
     * Get list of all backups
     */
    public function listBackups(): array
    {
        $storagePath = storage_path('app/' . $this->backupPath);
        
        if (!file_exists($storagePath)) {
            return [];
        }
        
        $files = glob($storagePath . '/*.sql');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'size_bytes' => filesize($file),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
        
        // Sort by date descending
        usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        
        return $backups;
    }
    
    /**
     * Delete a backup file
     */
    public function delete(string $filename): bool
    {
        $filePath = storage_path('app/' . $this->backupPath . '/' . basename($filename));
        
        if (file_exists($filePath) && str_ends_with($filename, '.sql')) {
            unlink($filePath);
            Log::info("Database backup deleted: {$filename}");
            return true;
        }
        
        return false;
    }
    
    /**
     * Restore database from backup
     */
    public function restore(string $filename): array
    {
        $filePath = storage_path('app/' . $this->backupPath . '/' . basename($filename));
        
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'Backup file not found'];
        }
        
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        
        // Try mysql command first
        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s %s %s < "%s" 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            $filePath
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            // Fallback: PHP-based restore
            return $this->phpRestore($filePath);
        }
        
        Log::info("Database restored from: {$filename}");
        
        return ['success' => true, 'message' => 'Database berhasil di-restore'];
    }
    
    /**
     * PHP-based restore fallback
     */
    protected function phpRestore(string $filePath): array
    {
        try {
            $sql = file_get_contents($filePath);
            
            // Split by semicolon but not inside quotes
            DB::unprepared($sql);
            
            Log::info("Database restored (PHP method) from: " . basename($filePath));
            
            return ['success' => true, 'message' => 'Database berhasil di-restore'];
        } catch (\Exception $e) {
            Log::error("Database restore failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Restore gagal: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get backup file path for download
     */
    public function getBackupPath(string $filename): ?string
    {
        $filePath = storage_path('app/' . $this->backupPath . '/' . basename($filename));
        
        if (file_exists($filePath) && str_ends_with($filename, '.sql')) {
            return $filePath;
        }
        
        return null;
    }
    
    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}