<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EncryptExistingData extends Command
{
    protected $signature = 'apms:encrypt-existing-data';
    protected $description = 'Encrypt existing plain-text data for fields with encrypted casts';

    public function handle(): int
    {
        $this->info('Encrypting existing plain-text data...');
        $encrypter = app(\Illuminate\Contracts\Encryption\Encrypter::class);
        $updated = 0;

        $tasks = [
            ['table' => 'users', 'column' => 'bank_account_number', 'label' => 'User bank_account_number'],
            ['table' => 'users', 'column' => 'npwp', 'label' => 'User NPWP'],
            ['table' => 'users', 'column' => 'nik', 'label' => 'User NIK'],
            ['table' => 'customers', 'column' => 'nik', 'label' => 'Customer NIK'],
        ];

        foreach ($tasks as $task) {
            $table = $task['table'];
            $column = $task['column'];
            $label = $task['label'];

            $rows = DB::table($table)
                ->whereNotNull($column)
                ->where($column, 'not like', 'eyJ%')
                ->get();

            $count = $rows->count();
            if ($count === 0) {
                $this->line("  [SKIP] {$label}: 0 rows need encryption");
                continue;
            }

            $bar = $this->output->createProgressBar($count);
            $bar->setFormat("  %current%/%max% [%bar%] %message%");
            $bar->setMessage("Encrypting {$label}...");
            $bar->start();

            foreach ($rows as $row) {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update([$column => $encrypter->encrypt($row->{$column})]);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->line("  [OK] {$label}: {$count} rows encrypted");
            $updated += $count;
        }

        $this->newLine();
        $this->info("Done. {$updated} total rows encrypted.");

        return Command::SUCCESS;
    }
}
