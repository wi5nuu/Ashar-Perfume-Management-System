<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection() { return Product::with(['category','brand'])->get(); }

    public function headings(): array { return ['Nama','SKU','Kategori','Merk','Harga Beli','Harga Jual','Harga Grosir','Satuan','Stok','Aktif']; }

    public function map($p): array {
        return [$p->name,$p->sku,$p->category?->name??'',$p->brand?->name??'',$p->purchase_price,$p->selling_price,$p->wholesale_price,$p->unit,$p->stock,$p->is_active?'Ya':'Tidak'];
    }
}
