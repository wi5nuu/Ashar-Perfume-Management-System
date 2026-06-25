<?php

namespace App\Services\CopilotIntents;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomingStockHandler implements CopilotIntentHandler
{
    public function handle(): string
    {
        $today = Carbon::today();
        $orders = PurchaseOrder::whereDate('expected_date', $today)
            ->whereIn('status', ['sent', 'partial'])
            ->with('supplier:id,name')
            ->with('items.product:id,name')
            ->get();

        if ($orders->isEmpty()) {
            return 'Tidak ada barang yang dijadwalkan masuk hari ini.';
        }

        $lines = [];
        foreach ($orders as $order) {
            $supplierName = $order->supplier ? $order->supplier->name : '(supplier tidak diketahui)';
            $lines[] = "- PO #{$order->po_number} dari {$supplierName}";
            foreach ($order->items as $item) {
                $productName = $item->product ? $item->product->name : '(produk dihapus)';
                $lines[] = "  • {$productName}: {$item->quantity_ordered} pcs";
            }
        }

        return "Barang yang akan masuk hari ini:\n" . implode("\n", $lines);
    }
}
