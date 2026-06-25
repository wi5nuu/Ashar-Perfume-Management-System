<?php

namespace App\Services\CopilotIntents;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $attendances = Attendance::whereDate('date', $today)
            ->orderBy('time_in')
            ->get();

        if ($attendances->isEmpty()) {
            return 'Belum ada data absensi untuk hari ini.';
        }

        $present = $attendances->whereNotNull('time_in');
        $absent = $attendances->whereNull('time_in');

        $lines = ["Absensi hari ini (" . $today->format('d/m/Y') . "):"];

        if ($present->isNotEmpty()) {
            $lines[] = 'Hadir:';
            foreach ($present as $a) {
                $timeIn = Carbon::parse($a->time_in)->format('H:i');
                $timeOut = $a->time_out ? ' — pulang ' . Carbon::parse($a->time_out)->format('H:i') : ' — belum pulang';
                $status = $a->status ? " ({$a->status})" : '';
                $lines[] = "- {$a->employee_name} (masuk {$timeIn}{$timeOut}){$status}";
            }
        }

        if ($absent->isNotEmpty()) {
            $lines[] = 'Belum hadir:';
            foreach ($absent as $a) {
                $reason = $a->status ? " — {$a->status}" : ($a->reason ? " — {$a->reason}" : '');
                $lines[] = "- {$a->employee_name}{$reason}";
            }
        }

        return implode("\n", $lines);
    }
}
