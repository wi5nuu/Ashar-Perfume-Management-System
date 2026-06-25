<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Resolves a named period (today, this_week, this_month, this_year) into
 * Carbon start/end date ranges plus a human-readable label.
 *
 * Eliminates duplicated period switch blocks across DashboardController,
 * ReportController, and any other reporting context.
 */
trait ResolvesPeriod
{
    /**
     * Resolve a period name to [startDate, endDate, label].
     *
     * @param string $period  One of: today, this_week, this_month, this_year
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today'     => [
                Carbon::today(),
                Carbon::today()->endOfDay(),
                'Today',
            ],
            'this_week' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                'This Week',
            ],
            'this_year' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
                'This Year',
            ],
            default     => [ // this_month
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'This Month',
            ],
        };
    }

    /**
     * Get a cache key prefix that includes the resolved period range.
     */
    protected function periodCacheKey(string $prefix, string $period): string
    {
        [$start, $end] = $this->resolvePeriod($period);
        return "{$prefix}.{$start->format('Ymd')}.{$end->format('Ymd')}";
    }
}
