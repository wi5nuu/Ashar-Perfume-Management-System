<?php

namespace App\Services\CopilotIntents;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftStatusHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $shifts = Shift::whereDate('opened_at', $today)
            ->with('user:id,name')
            ->orderBy('opened_at')
            ->get();

        if ($shifts->isEmpty()) {
            return 'Belum ada shift yang dibuka hari ini.';
        }

        $openCount = $shifts->whereNull('closed_at')->count();
        $closedCount = $shifts->whereNotNull('closed_at')->count();

        $lines = ["Shift hari ini: {$shifts->count()} total ({$openCount} masih buka, {$closedCount} sudah tutup)"];
        foreach ($shifts as $s) {
            $userName = $s->user ? $s->user->name : '(user dihapus)';
            $opened = Carbon::parse($s->opened_at)->format('H:i');
            $closed = $s->closed_at ? Carbon::parse($s->closed_at)->format('H:i') : 'masih buka';
            $total = $s->closed_at
                ? round((Carbon::parse($s->closed_at)->diffInMinutes(Carbon::parse($s->opened_at))) / 60, 1) . ' jam'
                : '-';
            $lines[] = "- {$userName}: buka {$opened}, " . ($s->closed_at ? "tutup {$closed}" : "masih buka") . " ({$total})";
        }

        return implode("\n", $lines);
    }
}
