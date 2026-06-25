<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public function createBackup(): string
    {
        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path("app/backups/{$filename}");

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $tables = DB::select('SHOW TABLES');
        $sql = "-- APMS Database Backup\n-- Generated: " . now() . "\n\n";

        foreach ($tables as $table) {
            $tableName = reset($table);
            $sql .= $this->dumpTable($tableName);
        }

        file_put_contents($path, $sql);

        Log::info("Database backup created: {$filename}");

        return $path;
    }

    protected function dumpTable(string $table): string
    {
        $sql = "-- Table: {$table}\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

        $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
        $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

        $rows = DB::table($table)->get();
        if ($rows->isEmpty()) return $sql;

        $columns = implode('`, `', array_keys(get_object_vars($rows->first())));
        $sql .= "INSERT INTO `{$table}` (`{$columns}`) VALUES\n";

        $values = [];
        foreach ($rows as $row) {
            $rowValues = [];
            foreach ((array) $row as $value) {
                $rowValues[] = $value === null ? 'NULL' : "'" . addslashes($value) . "'";
            }
            $values[] = '(' . implode(', ', $rowValues) . ')';
        }

        $sql .= implode(",\n", $values) . ";\n\n";

        return $sql;
    }

    public function cleanupOldBackups(int $keepDays = 30): int
    {
        $count = 0;
        $files = Storage::files('backups');

        foreach ($files as $file) {
            $timestamp = Storage::lastModified($file);
            if ($timestamp < now()->subDays($keepDays)->timestamp) {
                Storage::delete($file);
                $count++;
            }
        }

        return $count;
    }
}
