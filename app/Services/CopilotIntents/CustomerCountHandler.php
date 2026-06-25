<?php

namespace App\Services\CopilotIntents;

use App\Models\Customer;
use Carbon\Carbon;

class CustomerCountHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $total = Customer::where('is_active', true)->count();
        $todayNew = Customer::whereDate('created_at', Carbon::today())->count();

        return "Total pelanggan aktif: {$total} orang. Pelanggan baru hari ini: {$todayNew} orang.";
    }
}
