<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class SupplierScorecardService
{
    public function calculate(Supplier $supplier): array
    {
        $totalOrders = PurchaseOrder::where('supplier_id', $supplier->id)->count();
        $totalReceipts = PurchaseReceipt::whereHas('purchaseOrder', fn($q) => $q->where('supplier_id', $supplier->id))->count();
        $onTime = PurchaseReceipt::whereHas('purchaseOrder', fn($q) => $q->where('supplier_id', $supplier->id))
            ->whereRaw('received_at <= expected_at')->count();
        $ontimeRate = $totalReceipts > 0 ? round(($onTime/$totalReceipts)*100, 1) : 0;

        $quality = DB::table('purchase_receipt_items')
            ->join('purchase_receipts','purchase_receipt_items.purchase_receipt_id','=','purchase_receipts.id')
            ->join('purchase_orders','purchase_receipts.purchase_order_id','=','purchase_orders.id')
            ->where('purchase_orders.supplier_id', $supplier->id)
            ->avg('quality_score') ?? 0;

        $responseTime = $supplier->purchaseOrders()->avg('lead_time_hours') ?? 0;
        $overall = round(($ontimeRate*0.4)+($quality*0.4)+(max(0,100-$responseTime)*0.2), 1);

        $grade = match(true){$overall>=90=>'A',$overall>=75=>'B',$overall>=60=>'C',$overall>=40=>'D',default=>'E'};

        return ['total_orders'=>$totalOrders,'on_time_rate'=>$ontimeRate,'quality_score'=>round($quality,1),'avg_response_hours'=>round($responseTime,1),'overall_score'=>$overall,'grade'=>$grade];
    }
}
