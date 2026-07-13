<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection() { return Customer::with('branch')->get(); }

    public function headings(): array { return ['Nama','Telepon','Email','Alamat','Tipe','Cabang','Aktif']; }

    public function map($c): array {
        return [$c->name,$c->phone,$c->email,$c->address,$c->type,$c->branch?->name??'',$c->is_active?'Ya':'Tidak'];
    }
}
