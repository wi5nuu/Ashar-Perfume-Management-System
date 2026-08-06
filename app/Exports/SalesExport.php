<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Use end-of-day time so transactions on the last day are included.
        // Without ' 23:59:59', MySQL compares against midnight and misses
        // all transactions that occurred after 00:00:00 on the end date.
        return Transaction::with(['customer', 'user', 'details'])
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate   . ' 23:59:59',
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal', 'Invoice', 'Customer', 'Kasir',
            'Subtotal', 'Diskon', 'Total', 'Dibayar',
            'Metode', 'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y H:i'),
            $row->invoice_number,
            $row->customer->name ?? 'Umum',
            $row->user->name ?? '-',
            (float) $row->subtotal,
            (float) $row->discount,
            (float) $row->total_amount,
            (float) $row->paid_amount,
            $row->payment_method ?? '-',
            $row->payment_status ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
