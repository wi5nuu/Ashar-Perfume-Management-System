<?php

namespace App\Services\CopilotIntents;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerOriginHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $customers = Customer::select('address')
            ->where('is_active', true)
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->get();

        if ($customers->isEmpty()) {
            return 'Tidak ada data alamat pelanggan untuk dianalisis.';
        }

        $regions = [];
        foreach ($customers as $c) {
            $address = trim($c->address);
            $parts = explode(',', $address);
            $region = trim($parts[0]);
            if ($region === '') continue;
            $regions[$region] = ($regions[$region] ?? 0) + 1;
        }

        if (empty($regions)) {
            return 'Tidak dapat menentukan asal daerah pelanggan dari data yang tersedia.';
        }

        arsort($regions);
        $lines = ['Sebaran pelanggan berdasarkan daerah (data alamat bebas):'];
        foreach (array_slice($regions, 0, 10) as $region => $count) {
            $pct = round(($count / array_sum($regions)) * 100);
            $lines[] = "- {$region}: {$count} pelanggan ({$pct}%)";
        }

        return implode("\n", $lines);
    }
}
