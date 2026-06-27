<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display payroll overview for a given month.
     */
    public function index()
    {
        Gate::authorize('manage_payroll');

        $month = request('month', date('Y-m'));

        $employees = User::where('role', '!=', 'owner')
            ->whereNotNull('branch_id')
            ->with([
                'payrollSettings',
                'payrolls' => function ($q) use ($month) {
                    $q->where('month', $month);
                },
            ])
            ->get();

        return view('payroll.index', compact('employees', 'month'));
    }

    /**
     * Generate payroll for all employees for a given month.
     *
     * BEFORE (NOT ATOMIC):
     *   Looped updateOrCreate without DB::transaction.
     *   If the loop failed halfway, some employees had payroll and others didn't.
     *
     * AFTER (ATOMIC):
     *   All payroll records are generated inside a single DB::transaction.
     *   If any record fails, the entire batch rolls back.
     */
    public function generate(Request $request)
    {
        Gate::authorize('manage_payroll');

        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $month = $request->month;

        $employees = User::where('role', '!=', 'owner')
            ->whereNotNull('branch_id')
            ->with('payrollSettings')
            ->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'No employees found to generate payroll.');
        }

        try {
            DB::transaction(function () use ($employees, $month) {
                foreach ($employees as $employee) {
                    $settings = $employee->payrollSettings;

                    $attendanceCount = Attendance::where('user_id', $employee->id)
                        ->whereMonth('date', Carbon::parse($month . '-01')->month)
                        ->whereYear('date', Carbon::parse($month . '-01')->year)
                        ->where('status', 'present')
                        ->count();

                    $basic     = (float) ($employee->basic_salary ?? 0);
                    $allowance = (float) ($settings?->allowance ?? 0);
                    $deduction = (float) ($settings?->deduction ?? 0);
                    $total     = ($basic + $allowance) - $deduction;

                    Payroll::where('user_id', $employee->id)->where('month', $month)->lockForUpdate()->first();
                    Payroll::updateOrCreate(
                        [
                            'user_id' => $employee->id,
                            'month'   => $month,
                        ],
                        [
                            'basic_salary'   => $basic,
                            'allowance'      => $allowance,
                            'deduction'      => $deduction,
                            'total_salary'   => max(0, $total),
                            'attendance_days' => $attendanceCount,
                            'status'         => 'pending',
                        ]
                    );
                }
            });

            Log::info('Payroll generated', [
                'month'     => $month,
                'employees' => $employees->count(),
                'user_id'   => auth()->id(),
            ]);

            return back()->with('success', "Payroll for {$month} generated successfully for {$employees->count()} employees.");
        } catch (\Exception $e) {
            Log::error('Payroll generation failed', [
                'month' => $month,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Payroll generation failed. No records were created. Please try again.');
        }
    }
}
