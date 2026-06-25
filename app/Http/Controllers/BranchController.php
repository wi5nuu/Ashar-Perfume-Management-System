<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Transaction;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Gate::authorize('manage_settings');
            return $next($request);
        });
    }

    public function index()
    {
        $period = request()->get('period', 'this_month');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        $branches = Branch::withCount('users')
            ->with(['transactions' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }, 'expenses' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($branch) use ($startDate, $endDate) {
                $branch->period_revenue  = $branch->transactions->sum('total_amount');
                $branch->period_expenses = $branch->expenses->sum('amount');
                $branch->period_profit   = $branch->period_revenue - $branch->period_expenses;
                $branch->today_revenue   = $branch->transactions
                    ->where('created_at', '>=', Carbon::today()->startOfDay())
                    ->sum('total_amount');
                return $branch;
            });

        $totalRevenue  = $branches->sum('period_revenue');
        $totalExpenses = $branches->sum('period_expenses');
        $totalProfit   = $totalRevenue - $totalExpenses;

        return view('branches.index', compact(
            'branches', 'totalRevenue', 'totalExpenses', 'totalProfit',
            'period', 'periodLabel', 'startDate', 'endDate'
        ));
    }

    public function create()
    {
        return view('branches.form', ['branch' => new Branch(), 'isEdit' => false]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:20|unique:branches,code',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'manager_name' => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'notes'        => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Cabang baru berhasil ditambahkan!');
    }

    public function show(Branch $branch)
    {
        $period = request()->get('period', 'this_month');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        $recentTransactions = $branch->transactions()
            ->with('customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(20)
            ->get();

        $revenue  = $branch->revenueForPeriod($startDate, $endDate);
        $expenses = $branch->expensesForPeriod($startDate, $endDate);

        // Monthly chart data for this branch
        $year = Carbon::now()->year;
        $monthlySales = $branch->transactions()
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as sales')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('sales', 'month');

        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = [
                'month' => Carbon::create()->month($m)->format('M'),
                'sales' => $monthlySales->get($m, 0),
            ];
        }

        $staff = $branch->users()->select('id', 'name', 'role')->get();

        return view('branches.show', compact(
            'branch', 'revenue', 'expenses', 'recentTransactions',
            'period', 'periodLabel', 'chartData', 'staff'
        ));
    }

    public function edit(Branch $branch)
    {
        return view('branches.form', ['branch' => $branch, 'isEdit' => true]);
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:20|unique:branches,code,' . $branch->id,
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:30',
            'manager_name' => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'notes'        => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Data cabang berhasil diperbarui!');
    }

    public function destroy(Branch $branch)
    {
        // Soft-disable: set is_active to false rather than deleting
        $branch->update(['is_active' => false]);
        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil dinonaktifkan.');
    }

    private function resolvePeriod(string $period): array
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::today()->endOfDay(), 'Hari Ini'];
            case 'this_week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(), 'Minggu Ini'];
            case 'this_year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear(), 'Tahun Ini'];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), 'Bulan Ini'];
        }
    }
}
