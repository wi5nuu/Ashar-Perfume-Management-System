<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitLossExport implements FromArray, WithHeadings, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $revenue = Transaction::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', '!=', 'cancelled')
            ->sum('total_amount');

        $cogs = Transaction::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', '!=', 'cancelled')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->sum(\DB::raw('transaction_details.purchase_price * transaction_details.quantity'));

        $expenses = Expense::whereBetween('date', [$this->startDate, $this->endDate])
            ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;

        return [
            ['Pendapatan (Revenue)', $revenue],
            ['HPP (COGS)', $cogs],
            ['Laba Kotor (Gross Profit)', $grossProfit],
            ['Beban Operasional (Expenses)', $expenses],
            ['Laba Bersih (Net Profit)', $netProfit],
        ];
    }

    public function headings(): array
    {
        return ['Keterangan', 'Jumlah (Rp)'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            5 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
