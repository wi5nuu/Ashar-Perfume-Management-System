<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class SupplierScorecardService
{
    public function calculate(Supplier $supplier): array
    {
        $totalOrders = PurchaseOrder::where('supplier_id', $supplier->id)->count();

        // GoodsReceipt uses purchase_order_id and received_date
        $totalReceipts = GoodsReceipt::whereHas('purchaseOrder', fn($q) => $q->where('supplier_id', $supplier->id))->count();

        // On-time: received_date <= expected_delivery_date on the purchase order
        $onTime = GoodsReceipt::whereHas('purchaseOrder', fn($q) => $q->where('supplier_id', $supplier->id))
            ->whereHas('purchaseOrder', fn($q) => $q->whereColumn(
                'purchase_orders.expected_delivery_date', '>=',
                'goods_receipts.received_date'
            ))->count();

        $ontimeRate = $totalReceipts > 0 ? round(($onTime / $totalReceipts) * 100, 1) : 0;

        // Quality score: average condition_notes rating is not tracked — default to 100 if no data
        $quality = 100.0;

        $responseTime = $supplier->purchaseOrders()->avg('lead_time_days') ?? 0;
        $overall = round(($ontimeRate * 0.6) + (max(0, 100 - ($responseTime * 4)) * 0.4), 1);

        $grade = match(true) {
            $overall >= 90 => 'A',
            $overall >= 75 => 'B',
            $overall >= 60 => 'C',
            $overall >= 40 => 'D',
            default        => 'E',
        };

        return [
            'total_orders'       => $totalOrders,
            'on_time_rate'       => $ontimeRate,
            'quality_score'      => $quality,
            'avg_response_days'  => round((float) $responseTime, 1),
            'overall_score'      => $overall,
            'grade'              => $grade,
        ];
    }
}
