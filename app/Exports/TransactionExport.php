<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionExport implements FromQuery, WithHeadings, WithMapping
{
    protected $from; protected $to; protected $branchId;

    public function __construct($from=null,$to=null,$branchId=null) { $this->from=$from; $this->to=$to; $this->branchId=$branchId; }

    public function query() {
        $q = Transaction::with(['user','branch','customer']);
        if($this->from) $q->whereDate('created_at','>=',$this->from);
        if($this->to) $q->whereDate('created_at','<=',$this->to);
        if($this->branchId) $q->where('branch_id',$this->branchId);
        return $q;
    }

    public function headings(): array { return ['Invoice','Tanggal','Pelanggan','Kasir','Cabang','Total','Metode','Status']; }

    public function map($t): array {
        return [$t->invoice_number,$t->created_at->format('d/m/Y H:i'),$t->customer?->name??'Umum',$t->user?->name??'-',$t->branch?->name??'-',$t->total_amount,$t->payment_method,$t->status];
    }
}
