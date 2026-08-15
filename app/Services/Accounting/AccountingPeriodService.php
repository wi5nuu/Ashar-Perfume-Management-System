<?php

namespace App\Services\Accounting;

use App\Models\AccountingPeriod;
use Carbon\CarbonImmutable;

class AccountingPeriodService
{
    /**
     * Resolve the open period for a given date.
     * If the business month has no period yet, create it on the fly.
     */
    public static function currentOrCreate(?string $forDate = null): AccountingPeriod
    {
        $date = $forDate ? CarbonImmutable::parse($forDate) : CarbonImmutable::now();

        $period = AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($period) {
            return $period;
        }

        return AccountingPeriod::firstOrCreate(
            ['name' => $date->format('F Y')],
            [
                'name' => $date->format('F Y'),
                'start_date' => $date->startOfMonth()->toDateString(),
                'end_date' => $date->endOfMonth()->toDateString(),
                'is_closed' => false,
            ]
        );
    }

    public static function resolveForDate(string $date): AccountingPeriod
    {
        $period = AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if (! $period) {
            throw new \DomainException("Tidak ada periode akuntansi yang mencakup tanggal {$date}.");
        }

        if ($period->is_closed) {
            throw new \DomainException("Periode {$period->name} sudah ditutup.");
        }

        return $period;
    }
}
