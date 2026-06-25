<?php

namespace App\Services\CopilotIntents;

use App\Models\Branch;

class BranchInfoHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            return 'Saat ini belum ada cabang yang terdaftar.';
        }

        $lines = [];
        $lines[] = 'Kami memiliki ' . $branches->count() . ' cabang aktif:';
        foreach ($branches as $b) {
            $city = $b->city ? " ({$b->city})" : '';
            $manager = $b->manager_name ? " — Manager: {$b->manager_name}" : '';
            $lines[] = "- {$b->name}{$city}{$manager}";
        }

        return implode("\n", $lines);
    }
}
