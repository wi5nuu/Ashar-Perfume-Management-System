<?php

namespace App\Services\Payroll;

class BpjsCalculator
{
    public function calculate(int $wage): array
    {
        $w = min($wage, 12000000);
        $jw = min($wage, 10000000);

        return [
            'bpjs_kes_employee' => round($w * 0.01),
            'bpjs_kes_employer' => round($w * 0.04),
            'jht_employee' => round($w * 0.02),
            'jht_employer' => round($w * 0.032),
            'jp_employee' => round($jw * 0.01),
            'jp_employer' => round($jw * 0.02),
            'jkk' => round($w * 0.0024),
            'jkm' => round($w * 0.003),
            'total_employee' => round($w*0.01 + $w*0.02 + $jw*0.01),
            'total_employer' => round($w*0.04 + $w*0.032 + $jw*0.02 + $w*0.0024 + $w*0.003),
        ];
    }
}
