<?php

namespace App\Services\CopilotIntents;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeInfoHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $total = User::count();
        $roles = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->orderByDesc('count')
            ->get();

        $today = Carbon::today();
        $presentToday = Attendance::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->count();
        $absentToday = Attendance::whereDate('date', $today)
            ->whereNull('time_in')
            ->count();

        $lines = ["Total karyawan: {$total} orang"];
        foreach ($roles as $r) {
            $lines[] = "- {$r->role}: {$r->count} orang";
        }

        $lines[] = '';
        $lines[] = "Kehadiran hari ini: {$presentToday} hadir" . ($absentToday > 0 ? ", {$absentToday} belum absen" : '');

        return implode("\n", $lines);
    }
}
