<?php

namespace App\Services\Payroll;

class DeductionService
{
    public function __construct(
        protected BpjsCalculator $bpjs,
        protected Pph21Calculator $pph,
    ) {}

    public function calculate(int $monthlySalary, string $maritalStatus='single', int $dependents=0, float $overtimeHours=0, bool $isHoliday=false): array
    {
        $ot = (new OvertimeCalculator)->calculate($monthlySalary, $overtimeHours, $isHoliday);
        $grossMonthly = $monthlySalary + $ot['pay'];
        $bpjsResult = $this->bpjs->calculate($grossMonthly);
        $pphResult = $this->pph->calculate($grossMonthly * 12, $maritalStatus, $dependents);

        $takeHome = $grossMonthly - $bpjsResult['total_employee'] - $pphResult['monthly_tax'];

        return [
            'base_salary' => $monthlySalary,
            'overtime_pay' => $ot['pay'],
            'gross_monthly' => round($grossMonthly),
            'bpjs_deduction' => $bpjsResult['total_employee'],
            'pph_deduction' => $pphResult['monthly_tax'],
            'total_deductions' => $bpjsResult['total_employee'] + $pphResult['monthly_tax'],
            'take_home_pay' => round($takeHome),
        ];
    }
}
