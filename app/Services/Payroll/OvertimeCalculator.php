<?php

namespace App\Services\Payroll;

class OvertimeCalculator
{
    public function calculate(int $baseSalary, float $hours, bool $isHoliday=false): array
    {
        $hourly = $baseSalary / 173;
        $rate = $isHoliday ? 2.0 : 1.5;
        $pay = $hourly * $rate * $hours;
        return ['hourly_rate'=>round($hourly,2),'multiplier'=>$rate,'hours'=>$hours,'pay'=>round($pay,2)];
    }
}
