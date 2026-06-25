<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Inventory::with(['product', 'branch'])
            ->orderBy('product_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Produk', 'Cabang', 'Stok', 'Min Stok',
            'Harga Beli', 'Batch', 'Kadaluarsa', 'Status',
        ];
    }

    public function map($row): array
    {
        $status = 'Normal';
        if ($row->current_stock <= 0) {
            $status = 'Habis';
        } elseif ($row->current_stock < $row->minimum_stock) {
            $status = 'Rendah';
        }

        if ($row->expiration_date && $row->expiration_date->isPast()) {
            $status = 'Kadaluarsa';
        }

        return [
            $row->product->name ?? '-',
            $row->branch->name ?? '-',
            $row->current_stock,
            $row->minimum_stock,
            (float) $row->cost_per_unit,
            $row->batch_number ?? '-',
            $row->expiration_date ? $row->expiration_date->format('d/m/Y') : '-',
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
