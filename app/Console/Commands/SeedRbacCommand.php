<?php

namespace App\Console\Commands;

use App\Services\Security\RbacService;
use Illuminate\Console\Command;

class SeedRbacCommand extends Command
{
    protected $signature = 'rbac:seed';
    protected $description = 'Seed default roles and permissions';

    public function handle(RbacService $rbac): int
    {
        $this->info('Seeding RBAC default roles and permissions...');
        $rbac->seedDefaults();
        $this->info('RBAC seeded successfully.');
        $this->warn('Run "php artisan cache:clear" to clear any cached permissions.');
        return Command::SUCCESS;
    }
}
