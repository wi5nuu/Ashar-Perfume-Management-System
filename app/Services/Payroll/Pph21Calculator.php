<?php

namespace App\Services\Payroll;

class Pph21Calculator
{
    public function calculate(int $annualIncome, string $status='single', int $dependents=0): array
    {
        $ptkp = $status === 'married' ? 58500000 : 54000000;
        $ptkp += min($dependents, 3) * 4500000;

        $pkp = max(0, $annualIncome - $ptkp);
        $tax = 0; $rem = $pkp;

        foreach([[0,60000000,0.05],[60000000,250000000,0.15],[250000000,500000000,0.25],[500000000,PHP_INT_MAX,0.30]] as [$min,$max,$r]) {
            if($rem<=0) break;
            $tax += min($rem, $max-$min) * $r;
            $rem -= min($rem, $max-$min);
        }

        return ['annual_income'=>$annualIncome,'ptkp'=>$ptkp,'pkp'=>$pkp,'annual_tax'=>round($tax),'monthly_tax'=>round($tax/12),'effective_rate'=>$annualIncome>0?round(($tax/$annualIncome)*100,2):0];
    }
}
