<?php

namespace App\Services\CopilotIntents;

use App\Models\Coupon;
use Carbon\Carbon;

class ActivePromosHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $now = Carbon::now();
        $promos = Coupon::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiration_date')->orWhere('expiration_date', '>=', $now);
            })
            ->get();

        if ($promos->isEmpty()) {
            return 'Tidak ada promo atau kupon yang aktif saat ini.';
        }

        $lines = ['Promo / kupon aktif saat ini:'];
        foreach ($promos as $p) {
            $desc = "{$p->code} — ";
            if ($p->type === 'percentage') {
                $desc .= "Diskon {$p->value}%";
            } else {
                $desc .= "Potongan Rp " . number_format($p->value, 0, ',', '.');
            }
            if ($p->max_usage) {
                $desc .= " (digunakan {$p->used_count}/{$p->max_usage})";
            }
            if ($p->expiration_date) {
                $desc .= ' — berlaku hingga ' . Carbon::parse($p->expiration_date)->format('d/m/Y');
            }
            $lines[] = "- {$desc}";
        }

        return implode("\n", $lines);
    }
}
