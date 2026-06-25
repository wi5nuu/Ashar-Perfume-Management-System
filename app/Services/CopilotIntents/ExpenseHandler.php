<?php

namespace App\Services\CopilotIntents;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $expenses = Expense::whereDate('created_at', $today)
            ->with('category:id,name')
            ->get();

        $total = $expenses->sum('amount');
        $count = $expenses->count();

        if ($count === 0) {
            return 'Tidak ada pengeluaran tercatat hari ini.';
        }

        $byCategory = $expenses->groupBy(fn($e) => $e->category ? $e->category->name : 'Tanpa kategori');
        $lines = ["Pengeluaran hari ini: {$count} transaksi, total Rp " . number_format($total, 0, ',', '.')];
        foreach ($byCategory as $cat => $items) {
            $subtotal = $items->sum('amount');
            $lines[] = "- {$cat}: Rp " . number_format($subtotal, 0, ',', '.');
        }

        return implode("\n", $lines);
    }
}
