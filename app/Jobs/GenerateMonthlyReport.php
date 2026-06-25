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
        // Simulasi proses berat generate PDF laporan
        Log::info("Mulai membuat laporan bulanan untuk {$this->month}/{$this->year} oleh User {$this->userId}");
        
        sleep(5); // Simulasi proses 5 detik
        
        Log::info("Laporan bulanan selesai dibuat!");
        
        // Disini kita bisa mem-broadcast event ke user bahwa laporan sudah siap di-download
        // event(new ReportGenerated($this->userId, $fileUrl));
    }
}
