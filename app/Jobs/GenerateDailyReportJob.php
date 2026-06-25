<?php

namespace App\Jobs;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDailyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();
        $totalSales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $totalTransactions = Transaction::whereDate('created_at', $today)->count();

        Log::info("Daily report for {$today->toDateString()}: {$totalTransactions} transactions, total Rp " . number_format($totalSales, 0, ',', '.'));
    }
}
