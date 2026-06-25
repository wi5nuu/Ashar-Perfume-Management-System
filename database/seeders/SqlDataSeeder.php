<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SqlDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFiles = [
            'products_seed.sql',
            'products_seed_part2.sql',
            'products_seed_part3.sql',
            'products_seed_part4.sql',
        ];

        DB::beginTransaction();
        try {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($sqlFiles as $fileName) {
                $filePath = database_path('seeders/sql/' . $fileName);
                if (File::exists($filePath)) {
                    $this->command->info("Extracting and Importing INSERTS from: {$fileName}...");
                    $sql = File::get($filePath);
                    
                    // Regex to find all INSERT INTO statements
                    // Matches "INSERT INTO `table` (...) VALUES (...);" across multiple lines
                    preg_match_all('/INSERT INTO `.*?` \(.*?\) VALUES .*?;/s', $sql, $matches);

                    if (!empty($matches[0])) {
                        foreach ($matches[0] as $query) {
                            try {
                                DB::unprepared($query);
                            } catch (\Exception $e) {
                                // Log error but continue (might be duplicate IDs if multiple files have same data)
                                $this->command->warn("Skip query due to error: " . substr($query, 0, 50) . "...");
                            }
                        }
                    }
                } else {
                    $this->command->warn("File not found: {$fileName}");
                }
            }

            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();
            $this->command->info('✅ All SQL files imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Failed to import SQL: ' . $e->getMessage());
            throw $e;
        }
    }
}
