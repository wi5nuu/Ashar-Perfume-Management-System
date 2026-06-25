<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Daily backup at 2 AM
        $schedule->command('backup:run')->dailyAt('02:00');
        
        // Check for low stock alerts every hour
        $schedule->call(function () {
            \App\Jobs\CheckLowStockJob::dispatch();
        })->hourly();
        
        // Check for expiring products daily
        $schedule->call(function () {
            \App\Jobs\CheckExpiringProductsJob::dispatch();
        })->daily();
        
        // Generate daily sales report at midnight
        $schedule->call(function () {
            \App\Jobs\GenerateDailyReportJob::dispatch();
        })->dailyAt('23:59');

        // Send scheduled email reports
        $schedule->call(function () {
            $mode = \App\Models\Setting::getValue('report_schedule', 'daily');
            if (in_array($mode, ['daily', 'both'])) {
                \App\Jobs\SendScheduledReports::dispatch('daily');
            }
        })->dailyAt('23:50');

        $schedule->call(function () {
            $mode = \App\Models\Setting::getValue('report_schedule', 'daily');
            if (in_array($mode, ['weekly', 'both'])) {
                \App\Jobs\SendScheduledReports::dispatch('weekly');
            }
        })->weeklyOn(1, '07:00'); // Monday 7 AM
        
        // Clean up old audit logs every Sunday at 3 AM
        $schedule->call(function () {
            \App\Services\Security\ActivityMonitor::cleanOldLogs();
        })->weeklyOn(0, '03:00');
        
        // Force password change for users with expired passwords
        $schedule->call(function () {
            \App\Models\User::whereNotNull('password_changed_at')
                ->where('password_changed_at', '<', now()->subDays(config('security.password_policy.max_age_days', 90)))
                ->update(['requires_password_change' => true]);
        })->dailyAt('04:00');
    }
    
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}