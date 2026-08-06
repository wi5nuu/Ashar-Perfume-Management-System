<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $month;
    public $year;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($month, $year, $userId)
    {
        $this->month = $month;
        $this->year = $year;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Mulai membuat laporan bulanan untuk {$this->month}/{$this->year} oleh User {$this->userId}");

        // TODO: Implement actual PDF/report generation here.
        // Removed sleep(5) — blocking a queue worker thread wastes worker capacity.
        // Use job chaining or events if you need to signal completion to the user.

        Log::info("Laporan bulanan selesai dibuat!");

        // Broadcast to the requesting user that the report is ready:
        // event(new ReportGenerated($this->userId, $fileUrl));
    }
}
