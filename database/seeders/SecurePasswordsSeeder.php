<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecurePasswordsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('credentials/default-login.json');
        if (!file_exists($path)) {
            $this->command->warn('credentials/default-login.json not found — skipping password update.');
            return;
        }

        $data = json_decode(file_get_contents($path), true);
        $updated = 0;

        $accounts = array_merge(
            $data['demo_accounts'] ?? [],
            $data['branch_admin_accounts']['accounts'] ?? [],
            $data['store_employees']['accounts'] ?? [],
        );

        foreach ($accounts as $account) {
            $user = User::where('email', $account['email'])->first();
            if ($user) {
                $user->password = Hash::make($account['password']);
                $user->save();
                $updated++;
            }
        }

        $this->command->info("Updated passwords for {$updated} users from credentials file.");
    }
}
